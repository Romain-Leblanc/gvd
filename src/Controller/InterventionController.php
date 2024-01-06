<?php

namespace App\Controller;

use App\Form\FiltreTable\FiltreTableInterventionType;
use App\Repository\InterventionRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterventionController extends AbstractController
{
    #[Route('/intervention', name: 'intervention_index', methods: ['GET', 'POST'])]
    public function index(InterventionRepository $interventionRepository, Request $request): Response
    {
        $lesInterventions = $interventionRepository->findBy([], ['id' => 'DESC']);

        $form = $this->createForm(FiltreTableInterventionType::class, $lesInterventions);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_intervention');
            // Si au moins un filtre a été saisi, on récupère les résultats
            if ($data['id_intervention'] !== "" || $data['date_intervention'] !== "" || $data['vehicule'] !== "" || $data['client'] !== ""|| $data['montant_ht'] !== "")
                $lesInterventions = $interventionRepository->filtreTableIntervention((array) $data);
        }

        return $this->render('intervention/index.html.twig', [
            'lesInterventions' => $lesInterventions,
            'formFiltreTable' => $form->createView()
        ]);
    }
}
