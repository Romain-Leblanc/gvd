<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Form\AddInterventionType;
use App\Form\FiltreTable\FiltreTableInterventionType;
use App\Repository\InterventionRepository;
use App\Repository\TypeEtatRepository;
use App\Repository\EtatRepository;
use App\Repository\VehiculeRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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

    #[Route('/intervention/add', name: 'intervention_add', methods: ['GET', 'POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $uneIntervention = new Intervention();
        $form = $this->createForm(AddInterventionType::class, $uneIntervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Redéfinit les valeurs par défaut
            $uneIntervention->setFkFacture(null);
            $uneIntervention->setDateCreation(new \DateTime());

            $entityManager->persist($uneIntervention);
            $entityManager->flush();

            return $this->redirectToRoute('intervention_index');
        }

        return $this->render('intervention/add.html.twig', [
            'errors' => $form->getErrors(true),
            'formAddIntervention' => $form->createView()
        ]);
    }

    #[Route('/intervention/data', name: 'intervention_data', methods: ['POST'])]
    public function data(VehiculeRepository $vehiculeRepository, TypeEtatRepository $typeEtatRepository, EtatRepository $etatRepository, Request $request, SerializerInterface $serializer)
    {
        // Récupère l'identifiant pour la requête
        $id = (int) $request->request->get('clientID');
        if (!empty($id) && $id !== 0) {
            // Renvoi la liste des véhicules fonctionnel du client pour Ajax au format JSON
            $liste = $vehiculeRepository->findBy([
                'fk_client' => $id,
                'fk_etat' => $etatRepository->findOneBy(['etat' => 'Fonctionnel',
                'fk_type_etat' => $typeEtatRepository->findOneBy(['type' => 'vehicule'])])
            ]);
            return $this->json(['donnees' => $serializer->serialize($liste, 'json', ['groups' => ['intervention_data']])]);
        }
        else {
            $this->addFlash('intervention', 'Cet accès est restreint.');
            return $this->redirectToRoute('intervention_index');
        }
    }
}