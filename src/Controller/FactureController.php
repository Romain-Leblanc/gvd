<?php

namespace App\Controller;

use App\Form\FiltreTable\FiltreTableFactureType;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FactureController extends AbstractController
{
    #[Route('/facture', name: 'facture_index', methods: ['GET', 'POST'])]
    public function index(FactureRepository $factureRepository, Request $request): Response
    {
        $lesFactures = $factureRepository->findBy([], ['id' => 'DESC']);;

        $form = $this->createForm(FiltreTableFactureType::class, $lesFactures);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données du formulaire de recherche
            $data = $request->request->get('filtre_table_facture');
            $filtre = [];
            if ($data['id_facture'] !== "" || $data['date_facture'] !== "" || $data['client'] !== "" || $data['montant_ht'] !== "")
                $lesFactures = $factureRepository->filtreTableFacture((array) $data);
        }

        return $this->render('facture/index.html.twig', [
            'lesFactures' => $lesFactures,
            'formFiltreTable' => $form->createView()
        ]);
    }
}
