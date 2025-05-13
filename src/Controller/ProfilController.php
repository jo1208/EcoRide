<?php

namespace App\Controller;

use App\Entity\Covoiturage;
use App\Form\EditProfilType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Service\AvisService;


class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profil/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }


    #[Route('/profil/modifier', name: 'app_profil_edit')]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        /** @var \App\Entity\User $user */

        $form = $this->createForm(EditProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('plainPassword')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }

            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $stream = fopen($photoFile->getPathname(), 'rb');
                $user->setPhoto(stream_get_contents($stream));
                fclose($stream);
            }

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour avec succès ✅');
            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profil/roles', name: 'app_choix_role', methods: ['POST'])]
    public function updateRoles(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $roles = $request->request->all('roles');

        // Vérifier que s'il veut devenir chauffeur, il a bien une voiture ET des préférences
        if (in_array('ROLE_CHAUFFEUR', $roles)) {
            if ($user->getVoitures()->isEmpty()) {
                $this->addFlash('danger', '🚗 Vous devez enregistrer au moins un véhicule pour devenir chauffeur.');
                return $this->redirectToRoute('app_profil');
            }

            if (!$user->getPreference()) {
                $this->addFlash('danger', '⚙️ Vous devez compléter vos préférences de conducteur pour devenir chauffeur.');
                return $this->redirectToRoute('app_profil');
            }
        }

        // Tout est OK ✅ On met à jour les rôles
        $user->setRoles($roles);
        $em->flush();

        $this->addFlash('success', 'Vos rôles ont été mis à jour avec succès ✅');
        return $this->redirectToRoute('app_profil');
    }


    #[Route('/profil/avis', name: 'app_profil_avis')]
    public function showAvis(AvisRepository $avisRepository): Response
    {
        $user = $this->getUser();

        // Avis reçus (trie les avis reçus par date de création, du plus récent au plus ancien)
        $avisRecus = $avisRepository->findBy(
            ['conducteur' => $user],
            ['createdAt' => 'DESC'] // Tri par date de création
        );

        // Avis donnés (trie les avis donnés par date de création, du plus récent au plus ancien)
        $avisRediges = $avisRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC'] // Tri par date de création
        );

        return $this->render('profil/avis.html.twig', [
            'avisRecus' => $avisRecus,
            'avisRediges' => $avisRediges,
        ]);
    }
}
