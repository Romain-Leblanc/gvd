<?php

namespace App\Controller;

use App\Repository\InterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InterventionController extends AbstractController
{
    #[Route('/intervention', name: 'intervention_index', methods: ['GET', 'POST'])]
    public function index(InterventionRepository $interventionRepository): Response
    {
        $lesInterventions = $interventionRepository->findAll();

        return $this->render('intervention/index.html.twig', [
            'lesInterventions' => $lesInterventions,
        ]);
    }
}
