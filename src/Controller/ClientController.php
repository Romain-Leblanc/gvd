<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\FiltreTable\FiltreTableClientType;
use App\Form\AddClientType;
use App\Form\EditClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    #[Route('/client/add', name: 'client_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $unClient = new Client();
        $form = $this->createForm(AddClientType::class, $unClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($unClient);
            $entityManager->flush();

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/add.html.twig', [
            'errors' => $form->getErrors(true),
            'formAddClient' => $form->createView()
        ]);
    }

    #[Route('/client/edit/{id}', name: 'client_edit', defaults: ['id' => 0], methods: ['GET', 'POST'])]
    public function edit(int $id, ClientRepository $clientRepository, Request $request): Response
    {
        $unClient = $clientRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $unClient == null) {
            $this->addFlash('client', 'Ce client n\'existe pas.');
            return $this->redirectToRoute('client_index');
        }
        $form = $this->createForm(EditClientType::class, $unClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $clientRepository->updateClient($unClient);

            return $this->redirectToRoute('client_index');
        }

        return $this->render('client/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formEditClient' => $form->createView()
        ]);
    }
}
