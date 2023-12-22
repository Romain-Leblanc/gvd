<?php

namespace App\Controller;

use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    #[Route('/facture', name: 'facture_index')]
    public function index(FactureRepository $factureRepository): Response
    {
        $lesFactures = $factureRepository->findAll();

        return $this->render('facture/index.html.twig', [
            'lesFactures' => $lesFactures
        ]);
    }
}
