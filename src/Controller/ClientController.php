<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'client_index')]
    public function index(ClientRepository $clientRepository): Response
    {
        $lesClients = $clientRepository->findAll();

        return $this->render('client/index.html.twig', [
            'lesClients' => $lesClients
        ]);
    }
}
