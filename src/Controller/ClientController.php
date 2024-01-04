<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Form\FiltreTable\FiltreTableClientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/client', name: 'client_index', methods: ['GET', 'POST'])]
    public function index(ClientRepository $clientRepository, Request $request): Response
    {
        $lesClients = $clientRepository->findAll();

        $form = $this->createForm(FiltreTableClientType::class, $lesClients);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_client');
            // Si au moins un filtre a été saisi, on récupère les résultats
            if ($data['id_client'] !== ""
                || $data['client'] !== ""
                || $data['coordonnees'] !== ""
                || $data['adresse_complete'] !== "") {
                $lesClients = $clientRepository->filtreTableClient((array) $data);
            }
        }

        return $this->render('client/index.html.twig', [
            'lesClients' => $lesClients,
            'formFiltreTable' => $form->createView()
        ]);
    }
}
