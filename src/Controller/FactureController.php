<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Form\AddFactureType;
use App\Form\FiltreTable\FiltreTableFactureType;
use App\Repository\FactureRepository;
use App\Repository\InterventionRepository;
use App\Repository\VehiculeRepository;
use App\Repository\ClientRepository;
use App\Repository\EtatRepository;
use App\Repository\TypeEtatRepository;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Parsing\Html;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    /**
     * @throws \Spipu\Html2Pdf\Exception\Html2PdfException
     */
    #[Route('/facture/add', name: 'facture_add', methods: ['GET', 'POST'])]
    public function add(
        InterventionRepository $interventionRepository,
        ClientRepository $clientRepository,
        TypeEtatRepository $typeEtatRepository,
        EtatRepository $etatRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        // Retourne la liste des interventions qui sont terminées
        $listeInterventions = $interventionRepository->findBy(['fk_etat' => $etatRepository->findOneBy(['etat' => 'Terminé'])->getId()]);

        // Si aucune intervention est terminée (et donc n'a pas besoin d'être facturé), alors on renvoie un message puis une redirection
        if(empty($listeInterventions)){
            $this->addFlash('facture', 'Aucun intervention à facturée.');
            return $this->redirectToRoute("facture_index");
        }

        // Création de l'objet Facture(), génération du formulaire d'ajout d'une Facture avec l'objet Facture et manipulation des données de l'objet Request
        $uneFacture = new Facture();
        $form = $this->createForm(AddFactureType::class, $uneFacture);
        $form->handleRequest($request);

        // Si le formulaire a bien été soumis et est validé
        if ($form->isSubmitted() && $form->isValid()) {
            // Si seulement un des 2 champs de paiement a été saisi, on génère une erreur
            // Sinon si les 2 sont vides, on met à jour la facture (cela laisse la possibilité de reporter un paiement d'une facture)
            if (
                ($uneFacture->getFkMoyenPaiement() !== null && $uneFacture->getDatePaiement() === null) ||
                ($uneFacture->getFkMoyenPaiement() == null && $uneFacture->getDatePaiement() !== null)
            ) {
                $message = "L'un des 2 champs de paiement a été saisi, veuillez remplir les 2 ou les laisser vide.";
                return $this->render('facture/ajout.html.twig', [
                    'errors' => $form->addError(new FormError($message))->getErrors(true),
                    'formAddFacture' => $form->createView(),
                    'listeInterventions' => $listeInterventions
                ]);
            }

            // On définit la date de la facture par défaut puis on persiste l'objet
            $uneFacture->setDateFacture(new \DateTime());

            // Récupère l'ID du client concerné par la facture
            $idClient = $request->request->get('add_facture')['client'];

            // Récupère la liste des interventions terminées du client qui ne sont pas facturées
            $liste = $interventionRepository->findInterventionByClientAndEtat($idClient);

            // Définis l'état à 'Facturé' aux interventions du client correspondant puis ajoute chaque intervention à la facture qui va être persistée
            foreach ($liste as $uneIntervention) {
                $uneIntervention->setFkEtat($etatRepository->findOneBy(['etat' => 'Facturé', 'fk_type_etat' => $typeEtatRepository->findOneBy(['type' => 'intervention'])]));
                $uneFacture->addIntervention($uneIntervention);
            }
            $entityManager->persist($uneFacture);
            $entityManager->flush();

            // Génère et enregistre le PDF
            $this->generatePdf($uneFacture);

            // Redirection de la page vers le tableau principal
            return $this->redirectToRoute('facture_index');
        }

        return $this->render('facture/add.html.twig', [
            'errors' => $form->getErrors(true),
            'formAddFacture' => $form->createView(),
            'listeInterventions' => $listeInterventions
        ]);
    }

    #[Route('/facture/download/{id}', name: 'facture_download', defaults: ['id' => 0], methods: ['GET'])]
    public function download(int $id, FactureRepository $factureRepository, Request $request): Response
    {
        // Récupère toutes les infos sur la facture
        $uneFacture = $factureRepository->findOneBy(['id' => $id]);

        // Si le paramètre est égale à zéro ou que les resultats du Repository est null, on renvoi au tableau principal correspondant
        if($id == 0 || $uneFacture == null) {
            $this->addFlash('facture', 'Cette facture n\'existe pas.');
            return $this->redirectToRoute('facture_index');
        }

        // Récupération puis affichage du PDF de la facture
        $chemin = $this->getParameter('kernel.project_dir')."/public/pdf_facture/".$id.".pdf";
        return $this->file($chemin, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }

    // Fonction qui génère le PDF à partir de l'objet Facture
    public function generatePdf(Facture $facture) {
        // Récupération du logo pour le PDF
        $logo = $this->getParameter('kernel.project_dir')."/public/images/logo_64.png";

        // Génération du contenu du PDF
        $html = $this->renderView('facture/donnees_pdf.html.twig', [
            'uneFacture' => $facture,
            'logo' => $logo
        ]);

        // Génération nom du fichier avec l'emplacement de sauvegarde du PDF sur le disque
        $fichier = $facture->getId().'.pdf';
        $chemin = $this->getParameter('kernel.project_dir')."/public/pdf_facture";
        $cheminComplet = $chemin."/".$fichier;

        // Génère le PDF final
        $pdf = new Html2Pdf('P', 'A4', 'fr');
        $pdf->pdf->setTitle("Facture n°".$facture->getId());
        $pdf->writeHTML($html);

        // Enregiste le PDF dans un fichier dans le dossier des factures
        $data = $pdf->pdf->getPDFData();
        file_put_contents($cheminComplet, $data);
    }

    #[Route('/facture/data', name: 'facture_data', methods: ['POST'])]
    public function data(InterventionRepository $interventionRepository, VehiculeRepository $vehiculeRepository, TypeEtatRepository $typeEtatRepository, EtatRepository $etatRepository, Request $request, SerializerInterface $serializer)
    {
        // Récupère l'identifiant pour la requête
        $id = (int) $request->request->get('clientID');
        // $id = 7;
        if (!empty($id) && $id !== 0) {
            // Renvoi la liste des interventions non facturés des véhicules du client pour Ajax au format JSON
            $liste = $interventionRepository->findInterventionByClientAndEtat($id);
            return $this->json(['donnees' => $serializer->serialize($liste, 'json', ['groups' => ['facture_data']])]);
        }
        else {
            $this->addFlash('facture', 'Cet accès est restreint.');
            return $this->redirectToRoute('facture_index');
        }
    }
}