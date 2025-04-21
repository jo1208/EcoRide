<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Form\CovoiturageType;
use App\Repository\CovoiturageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class CovoiturageController extends AbstractController
{
    #[Route('/covoiturage', name: 'app_covoiturage')]
    public function index(Request $request, CovoiturageRepository $repo): Response
    {
        $filters = [
            'lieu_depart' => $request->query->get('lieu_depart'),
            'lieu_arrivee' => $request->query->get('lieu_arrivee'),
            'date' => $request->query->get('date'),
            'prix_max' => $request->query->get('prix_max'),
            'duree_max' => $request->query->get('duree_max'),
            'note_min' => $request->query->get('note_min'),
            'ecologique' => $request->query->get('ecologique'),
        ];

        $trajets = [];
        $formIncomplete = false;
        $propositionNouvelleDate = null;

        if ($filters['lieu_depart'] && $filters['lieu_arrivee'] && $filters['date']) {
            $trajets = $repo->findWithFilters($filters);
        }

        // ✅ Filtrage durée_max en PHP
        if (!empty($filters['duree_max'])) {
            $trajets = array_filter($trajets, function ($trajet) use ($filters) {
                $depart = $trajet->getHeureDepart();
                $arrivee = $trajet->getHeureArrivee();

                if (!$depart || !$arrivee) {
                    return false;
                }

                $minutesDepart = ($depart->format('H') * 60) + $depart->format('i');
                $minutesArrivee = ($arrivee->format('H') * 60) + $arrivee->format('i');
                $duree = $minutesArrivee - $minutesDepart;

                return $duree <= $filters['duree_max'];
            });
        }

        // ✅ Si aucun trajet, proposer une nouvelle date
        if (empty($trajets)) {
            if ($filters['lieu_depart'] && $filters['lieu_arrivee']) {
                $firstAvailable = $repo->findFirstAvailableMatchingLocation($filters);
                if ($firstAvailable) {
                    $propositionNouvelleDate = $firstAvailable->getDateDepart();
                }
            }
        } else {
            $formIncomplete = true;
        }


        return $this->render('covoiturage/index.html.twig', [
            'trajets' => $trajets,
            'formIncomplete' => $formIncomplete,
            'propositionNouvelleDate' => $propositionNouvelleDate,
        ]);
    }




    #[Route('/covoiturage/nouveau', name: 'covoiturage_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user || !in_array('ROLE_CHAUFFEUR', $user->getRoles())) {
            $this->addFlash('danger', 'Vous devez être chauffeur pour créer un trajet.');
            return $this->redirectToRoute('app_profil');
        }

        // Vérifier les crédits
        if ($user->getCredits() < 2) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de crédits pour créer un trajet. (2 crédits requis)');
            return $this->redirectToRoute('app_profil');
        }

        $trajet = new Covoiturage();
        $form = $this->createForm(CovoiturageType::class, $trajet, [
            'user' => $this->getUser(), // ✅ Passe l'utilisateur connecté ici !
        ]);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trajet->setConducteur($user);

            // Retirer 2 crédits
            $user->setCredits($user->getCredits() - 2);

            $em->persist($trajet);
            $em->flush();

            $this->addFlash('success', 'Trajet créé avec succès ✅ (2 crédits ont été déduits)');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('covoiturage/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/profil/historique', name: 'app_historique')]
    public function historique(CovoiturageRepository $repo): Response
    {
        $user = $this->getUser();

        // Trajets terminés ou annulés en tant que conducteur
        $trajetsConducteur = $repo->createQueryBuilder('c')
            ->where('c.conducteur = :user')
            ->andWhere('c.date_depart < :now OR c.statut = :annule')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->setParameter('annule', 'Annulé')
            ->orderBy('c.date_depart', 'DESC')
            ->getQuery()
            ->getResult();

        // Trajets terminés ou annulés en tant que passager
        $trajetsPassager = $repo->createQueryBuilder('c')
            ->join('c.passagers', 'p')
            ->where('p = :user')
            ->andWhere('c.date_depart < :now OR c.statut = :annule')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTime())
            ->setParameter('annule', 'Annulé')
            ->orderBy('c.date_depart', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('profil/historique.html.twig', [
            'trajetsConducteur' => $trajetsConducteur,
            'trajetsPassager' => $trajetsPassager,
        ]);
    }


    #[Route('/profil/mes-trajets', name: 'app_mes_trajets')]
    public function trajetsAVenir(CovoiturageRepository $repo): Response
    {
        $user = $this->getUser();
        $now = new \DateTimeImmutable('today');

        // Trajets à venir comme conducteur
        $trajetsConducteur = $repo->createQueryBuilder('c')
            ->where('c.conducteur = :user')
            ->andWhere('c.date_depart >= :now')
            ->andWhere('(c.statut IS NULL OR c.statut != :annule)')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->setParameter('annule', 'Annulé')
            ->orderBy('c.date_depart', 'ASC')
            ->getQuery()
            ->getResult();

        // Trajets à venir comme passager
        $trajetsPassager = $repo->createQueryBuilder('c')
            ->join('c.passagers', 'p')
            ->where('p = :user')
            ->andWhere('c.date_depart >= :now')
            ->andWhere('(c.statut IS NULL OR c.statut != :annule)')
            ->setParameter('user', $user)
            ->setParameter('now', $now)
            ->setParameter('annule', 'Annulé')
            ->orderBy('c.date_depart', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('profil/mes_trajets.html.twig', [
            'trajetsConducteur' => $trajetsConducteur,
            'trajetsPassager' => $trajetsPassager,

        ]);
    }

    #[Route('/trajet/{id}/annuler', name: 'app_annuler_trajet_conducteur', methods: ['POST', 'GET'])]
    public function annulerTrajetConducteur(Covoiturage $trajet, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $user = $this->getUser();

        if ($trajet->getConducteur() !== $user) {
            $this->addFlash('danger', 'Accès interdit.');
            return $this->redirectToRoute('app_mes_trajets');
        }

        $nombrePassagers = count($trajet->getPassagers());
        $prixParPersonne = $trajet->getPrixPersonne();
        $totalCreditsGagnes = $nombrePassagers * $prixParPersonne;

        // ✅ Rembourser tous les passagers
        foreach ($trajet->getPassagers() as $passager) {
            $passager->setCredits($passager->getCredits() + $prixParPersonne);
            $em->persist($passager);

            $email = (new Email())
                ->from('ecoride.dev@gmail.com')
                ->to($passager->getEmail())
                ->subject('🚗 Annulation de votre covoiturage')
                ->html("
                <p>Bonjour <strong>{$passager->getPrenom()}</strong>,</p>
                <p>Votre trajet <strong>{$trajet->getLieuDepart()}</strong> → <strong>{$trajet->getLieuArrivee()}</strong> prévu le <strong>{$trajet->getDateDepart()->format('d/m/Y')}</strong> a été annulé par le conducteur.</p>
                <p>Vos crédits ont été remboursés automatiquement.</p>
                <p>Merci de votre confiance sur EcoRide 🌿</p>
            ");

            $mailer->send($email);
        }


        // ✅ Rendre toutes les places disponibles
        $trajet->setNbPlace(0);

        // ✅ Rembourser les 2 crédits de création
        $user->setCredits($user->getCredits() + 2);

        // ❗ Retirer les crédits gagnés sur les passagers
        $user->setCredits($user->getCredits() - $totalCreditsGagnes);

        // 🔒 Sécurité : éviter crédits négatifs
        if ($user->getCredits() < 0) {
            $user->setCredits(0);
        }

        // ✅ Mettre le statut du trajet à "Annulé"
        $trajet->setStatut('Annulé');

        $em->persist($trajet);
        $em->persist($user);
        $em->flush();

        $this->addFlash('success', 'Trajet annulé ✅ Les passagers sont remboursés et vos crédits gagnés ont été retirés.');
        return $this->redirectToRoute('app_mes_trajets');
    }




    #[Route('/trajet/{id}/quitter', name: 'app_annuler_trajet_passager', methods: ['POST', 'GET'])]
    public function quitterTrajetPassager(Covoiturage $trajet, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        // Sécurité : vérifier si l'utilisateur participe à ce trajet
        if (!$trajet->getPassagers()->contains($user)) {
            $this->addFlash('danger', 'Vous n\'êtes pas inscrit à ce trajet.');
            return $this->redirectToRoute('app_mes_trajets');
        }

        $prix = $trajet->getPrixPersonne();
        $conducteur = $trajet->getConducteur();

        // ✅ Rendre les crédits au passager
        $user->setCredits($user->getCredits() + $prix);

        // ✅ Retirer les crédits du conducteur
        if ($conducteur) {
            $conducteur->setCredits($conducteur->getCredits() - $prix);
            $em->persist($conducteur);
        }

        // ✅ Retirer l'utilisateur de la liste des passagers
        $trajet->removePassager($user);

        // ✅ Remettre une place disponible
        $trajet->setNbPlace($trajet->getNbPlace() + 1);

        // Sauvegarder tout ça
        $em->persist($user);
        $em->persist($trajet);
        $em->flush();

        $this->addFlash('success', '🚗 Vous êtes désinscrit du trajet et vos crédits ont été remboursés.');
        return $this->redirectToRoute('app_mes_trajets');
    }


    #[Route('/covoiturage/{id}/participer', name: 'app_participer_covoiturage', methods: ['POST'])]
    public function participer(Covoiturage $covoiturage, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Veuillez vous connecter pour participer.');
            return $this->redirectToRoute('app_login');
        }

        if ($covoiturage->getPassagers()->contains($user)) {
            $this->addFlash('warning', 'Vous participez déjà à ce trajet.');
        } elseif ($covoiturage->getNbPlace() <= 0) {
            $this->addFlash('danger', 'Ce trajet est complet.');
        } elseif ($covoiturage->getConducteur() === $user) {
            $this->addFlash('danger', 'Vous êtes le conducteur de ce trajet.');
        } elseif ($user->getCredits() < $covoiturage->getPrixPersonne()) {
            $this->addFlash('danger', 'Vous n\'avez pas assez de crédits.');
        } else {
            $prix = $covoiturage->getPrixPersonne();

            // ✅ Le passager paye
            $user->setCredits($user->getCredits() - $prix);

            // ✅ Le conducteur reçoit l'argent
            $conducteur = $covoiturage->getConducteur();
            if ($conducteur) {
                $conducteur->setCredits($conducteur->getCredits() + $prix);
                $em->persist($conducteur);
            }

            // ✅ Ajouter le passager au trajet
            $covoiturage->addPassager($user);

            // ✅ Diminuer le nombre de places
            $covoiturage->setNbPlace($covoiturage->getNbPlace() - 1);

            // On enregistre tout ça
            $em->persist($user);
            $em->persist($covoiturage);
            $em->flush();

            $this->addFlash('success', '✅ Participation confirmée. ' . $prix . ' crédits utilisés.');
        }

        return $this->redirectToRoute('app_mes_trajets');
    }




    #[Route('/covoiturage/{id}', name: 'covoiturage_show')]
    public function show(Covoiturage $covoiturage): Response
    {
        $conducteur = $covoiturage->getConducteur();
        $voiture = $covoiturage->getVoiture();
        $avis = $conducteur->getAvis();
        $preference = $conducteur->getPreference();

        return $this->render('covoiturage/show.html.twig', [
            'covoiturage' => $covoiturage,
            'conducteur' => $conducteur,
            'voiture' => $voiture,
            'avis' => $avis,
            'preference' => $preference,
        ]);
    }



    #[Route('/test-email', name: 'app_test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('ecoride.dev@gmail.com') // <- Expéditeur GMAIL
            ->to('jonathan.pina1208@gmail.com') // <- Toi ou un autre email
            ->subject('Test Email via Gmail ✅')
            ->html('<p>cest bon ca marche</p>');

        $mailer->send($email);

        return new Response('Email envoyé ! Vérifie ta boîte ');
    }
}
