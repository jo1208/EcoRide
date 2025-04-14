<?php


namespace App\Controller;

use App\Repository\CovoiturageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

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

        $trajets = $repo->findWithFilters($filters);

        return $this->render('covoiturage/index.html.twig', [
            'trajets' => $trajets,
        ]);
    }
}
