<?php

namespace App\Controller\Admin;

use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/facture')]
class FactureController extends AbstractController
{
    #[Route('/', name: 'facture_admin_index', methods: ['GET'])]
    public function index(FactureRepository $factureRepository): Response
    {
        // Tableau qui récupère le mois traduit en français
        $tableauMois = $this->getMonthYear();
        $tableauResultat = [];

        /**
         * Boucle sur chaque mois pour définir sa traduction et son montant HT si égal à zéro.
         * Le groupement par mois de l'année n'est pas utilisé parce que
         * la requête ne retournera rien si aucune facture d'un certain mois n'existe pas
         */
        foreach ($tableauMois as $unMois) {
            // Récupère les infos des factures du mois de l'année actuelle
            $resultat = $factureRepository->findByMois(date('Y'), $unMois);
            // Traduit le mois de l'année si il est présent dans le tableau des traductions 
            if(array_search($unMois, $tableauMois)) {
                $resultat['mois'] = array_search($unMois, $tableauMois);
            }
            // Définit les montants à zéro si null
            if(is_null($resultat['montant'])) {
                $resultat['nombre'] = 0;
                $resultat['montant'] = 0;
            }
            array_push($tableauResultat, $resultat);
        }

        return $this->render('admin/facture/index.html.twig', [
            'lesMoisFactures' => $tableauResultat,
        ]);
    }

    // Retourne les mois de l'année traduit
    private function getMonthYear()
    {
        return [
            'Janvier' => 'January',
            'Février' => 'February',
            'Mars' => 'March',
            'Avril' => 'April',
            'Mai' => 'May',
            'Juin' => 'June',
            'Juillet' => 'July ',
            'Août' => 'August',
            'Septembre' => 'September',
            'Octobre' => 'October',
            'Novembre' => 'November',
            'Décembre' => 'December'
        ];
    }
}
