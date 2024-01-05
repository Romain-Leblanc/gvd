<?php

namespace App\Controller;

use App\Entity\Vehicule;
use App\Repository\VehiculeRepository;
use App\Form\AddVehiculeType;
use App\Form\EditVehiculeType;
use App\Form\FiltreTable\FiltreTableVehiculeType;
use App\Repository\ModeleRepository;
use App\Repository\InterventionRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VehiculeController extends AbstractController
{
    #[Route('/vehicule', name: 'vehicule_index', methods: ['GET', 'POST'])]
    public function index(VehiculeRepository $vehiculeRepository, Request $request): Response
    {
        $lesVehicules = $vehiculeRepository->findAll();

        $form = $this->createForm(FiltreTableVehiculeType::class, $lesVehicules);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_vehicule');
            $filtre = [];
            // Vérifie si un filtre a été saisi puis définit ses valeurs
            if ($data['id_vehicule'] !== "") $filtre['id'] = (int) $data['id_vehicule'];
            if ($data['client'] !== "") $filtre['fk_client'] = (int) $data['client'];
            if ($data['vehicule'] !== "") $filtre['fk_modele'] = (int) $data['vehicule'];
            if ($data['immatriculation'] !== "") $filtre['immatriculation'] = (string) $data['immatriculation'];
            if ($data['etat'] !== "") $filtre['fk_etat'] = (int) $data['etat'];
            // Si un filtre a été saisi, on récupère les nouvelles valeurs
            if (isset($filtre)) {
                $lesVehicules = $vehiculeRepository->findBy($filtre);
            }
        }

        return $this->render('vehicule/index.html.twig', [
            'lesVehicules' => $lesVehicules,
            'formFiltreTable' => $form->createView()
        ]);
    }

    #[Route('/vehicule/add', name: 'vehicule_add', methods: ['GET', 'POST'])]
    public function add(VehiculeRepository $vehiculeRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $unVehicule = new Vehicule();
        $form = $this->createForm(AddVehiculeType::class, $unVehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Si l'immatriculation saisie existe déjà et que l'identifiant du véhiculé modifié est différent
            // de celui du véhicule qui possède l'immatriculation existante, on génère une erreur
            $id = $vehiculeRepository->findOneBy(['immatriculation' => $unVehicule->getImmatriculation()]);
            if(isset($id) && $unVehicule->getId() != $id->getId()) {
                $message = "Cette immatriculation existe déjà pour un autre véhicule.";
                return $this->render('vehicule/add.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formAddVehicule' => $form->createView()
                ]);
            }
            else {
                // Si aucun identifiant de modèle, on génère une erreur
                if(is_null($unVehicule->getFkModele()) || $unVehicule->getFkModele() == "") {
                    $message = "Veuillez sélectionner un modèle de véhicule.";
                    return $this->render('vehicule/add.html.twig', [
                        'errors' => $form->addError(new FormError($message))->getErrors(true),
                        'formAddVehicule' => $form->createView()
                    ]);
                }
                // Sinon on insère
                else {
                    $entityManager->persist($unVehicule);
                    $entityManager->flush();
                    return $this->redirectToRoute('vehicule_index');
                }
            }
        }

        return $this->render('vehicule/add.html.twig', [
            'formAddVehicule' => $form->createView(),
            'errors' => $form->getErrors(true),
        ]);
    }

    #[Route('/vehicule/edit/{id}', name: 'vehicule_edit', defaults: ['id' => 0], methods: ['GET', 'POST'])]
    public function edit(int $id, VehiculeRepository $vehiculeRepository, InterventionRepository $interventionRepository, Request $request): Response
    {
        $unVehicule = $vehiculeRepository->find($id);
        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $unVehicule == null) {
            $this->addFlash('vehicule', 'Ce véhicule n\'existe pas.');
            return $this->redirectToRoute('vehicule_index');
        }
        // Si le véhicule est déjà dans une intervention, on ne peut pas modifier à quel client appartient ce véhicule, ni la marque et le modèle.
        $options = $interventionRepository->findBy(['fk_vehicule' => $unVehicule->getId()]);
        $form = $this->createForm(EditVehiculeType::class, $unVehicule, ["intervention" => $options]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si l'immatriculation saisie existe déjà et que l'identifiant du véhiculé modifié est différent
            // de celui du véhicule qui possède l'immatriculation existante, on génère une erreur
            $id = $vehiculeRepository->findOneBy(['immatriculation' => $unVehicule->getImmatriculation()]);
            if(isset($id) && $unVehicule->getId() != $id->getId()) {
                $message = "Cette immatriculation existe déjà pour un autre véhicule.";
                return $this->render('vehicule/edit.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formEditVehicule' => $form->createView()
                ]);
            }
            // Sinon on met à jour les données
            else {
                $vehiculeRepository->updateVehicule($unVehicule);
                return $this->redirectToRoute('vehicule_index');
            }
        }

        return $this->render('vehicule/edit.html.twig', [
            'errors' => $form->getErrors(true),
            'formEditVehicule' => $form->createView()
        ]);
    }

    #[Route('/vehicule/data', name: 'vehicule_data', methods: ['POST'])]
    public function data(ModeleRepository $modeleRepository, Request $request, SerializerInterface $serializer): Response
    {
        // Récupère l'identifiant pour la requête
        $id = (int) $request->request->get('marqueID');
        if (!empty($id) && $id !== 0) {
            // Renvoi la liste des modèles de la marque de voiture pour Ajax au format JSON
            $liste = $modeleRepository->findBy(['fk_marque' => $id]);
            return $this->json(['donnees' => $serializer->serialize($liste, 'json', ['groups' => ['main']])]);
        }
        else {
            $this->addFlash('vehicule', 'Cet accès est restreint.');
            return $this->redirectToRoute('vehicule_index');
        }
    }
}
