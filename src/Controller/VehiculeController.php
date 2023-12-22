<?php

namespace App\Controller;

use App\Repository\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehiculeController extends AbstractController
{
    #[Route('/vehicule', name: 'vehicule_index')]
    public function index(): Response
    {
        $lesVehicules = $vehiculeRepository->findAll();

        return $this->render('vehicule/index.html.twig', [
            'lesVehicules' => $lesVehicules
        ]);
    }
}
