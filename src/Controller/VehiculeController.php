<?php

namespace App\Controller;

use App\Repository\VehiculeRepository;
use App\Form\FiltreTable\FiltreTableVehiculeType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
