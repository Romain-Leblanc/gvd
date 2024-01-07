<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Form\AddInterventionType;
use App\Form\EditInterventionType;
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

    #[Route('/intervention/edit/{id}', name: 'intervention_edit', defaults: ['id' => 0], methods: ['GET', 'POST'])]
    public function edit(int $id, InterventionRepository $interventionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $uneIntervention = $interventionRepository->find($id);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneIntervention == null) {
            $this->addFlash('intervention', 'Cette intervention n\'existe pas.');
            return $this->redirectToRoute('intervention_index');
        }

        $form = $this->createForm(EditInterventionType::class, $uneIntervention);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'intervention s'apprête à être terminée et que le montant HT est à zéro, on génère une erreur
            if($uneIntervention->getFkEtat()->getEtat() == "Terminé" && $uneIntervention->getMontantHt() == 0) {
                $message = "L'état de l'intervention est défini sur 'Terminé' mais le montant HT est à zéro.";
                return $this->render('intervention/edit.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formEditIntervention' => $form->createView()
                ]);
            }
            // Sinon si le type d'état de l'intervention concerne ceux des véhicules, on génère une erreur
            elseif ($uneIntervention->getFkEtat()->getFkTypeEtat()->getType() == "vehicule") {
                $message = "L'état de l'intervention doit concernés ceux pour les interventions.";
                return $this->render('intervention/edit.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formEditIntervention' => $form->createView()
                ]);
            }
            else {
                $interventionRepository->updateIntervention($uneIntervention);
                return $this->redirectToRoute('intervention_index');
            }
        }

        return $this->render('intervention/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formEditIntervention' => $form->createView()
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