<?php

namespace App\Twig\Extension;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FunctionExtension extends AbstractExtension
{
    public function __construct()
    {
        $this->listeMenus = ["intervention", "vehicule", "client", "facture"];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('adresseComplete', [$this, 'adresseComplete']),
            new TwigFunction('adresseCompleteFacture', [$this, 'adresseCompleteFacture']),
            new TwigFunction('nomPrenom', [$this, 'nomPrenom']),
            new TwigFunction('numTelEmail', [$this, 'numTelEmail']),
            new TwigFunction('affichagePlusieursValeurs', [$this, 'affichagePlusieursValeurs']),
            new TwigFunction('marqueModele', [$this, 'marqueModele']),
            new TwigFunction('marqueModeleFacture', [$this, 'marqueModeleFacture']),
            new TwigFunction('formatMontantEuros', [$this, 'formatMontantEuros']),
            new TwigFunction('formatTotalTVA', [$this, 'formatTotalTVA']),
            new TwigFunction('formatTotalTTC', [$this, 'formatTotalTTC']),
            new TwigFunction('dureeIntervention', [$this, 'dureeIntervention']),
            new TwigFunction('dateEnFrancais', [$this, 'dateEnFrancais']),
            new TwigFunction('titrePage', [$this, 'titrePage']),
            new TwigFunction('menuActif', [$this, 'menuActif']),
        ];
    }

    // Retourne l'adresse complète
    function adresseComplete(string $adresse, string $suite_adresse = null, string $code_postal, string $ville){
        if(is_null($suite_adresse)) {
            return ucfirst($adresse)." - ".$code_postal." ".mb_strtoupper($ville);
        }
        else {
            return ucfirst($adresse)." ".mb_strtoupper($suite_adresse)." - ".$code_postal." ".mb_strtoupper($ville);
        }
    }

    // Retourne l'adresse complète pour le PDF d'une facture
    function adresseCompleteFacture(string $adresse, string $suite_adresse = null, string $code_postal, string $ville){
        if($suite_adresse == null){
            return ucfirst($adresse)."\n".$code_postal." ".mb_strtoupper($ville);
        }
        else {
            return ucfirst($adresse)." ".mb_strtoupper($suite_adresse)."\n".$code_postal." ".mb_strtoupper($ville);
        }
    }

    // Retourne le nom et prénom
    function nomPrenom(string $nom, string $prenom){
        return mb_strtoupper($nom)." ".ucfirst($prenom);
    }

    // Retourne le n° téléphone et l'email
    function numTelEmail(string $tel, string $email){
        if($tel[2] !== " "){
            return $tel[0].$tel[1]." ".$tel[2].$tel[3]." ".$tel[4].$tel[5]." ".$tel[6].$tel[7]." ".$tel[8].$tel[9]."<br><span class='text-primary'>".$email."</span>";
        }
        else {
            return $tel."<br>".$email;
        }
    }

    // Retourne un caractère "s" s'il y la valeur est supérieur à 1.
    function affichagePlusieursValeurs(int $valeur) {
        if($valeur > 1) {
            return "s";
        }
        else {
            return "";
        }
    }

    // Retourne la marque et le modèle
    function marqueModele(string $marque, string $modele){
        return mb_strtoupper($marque." ".$modele);
    }

    // Retourne la marque et le modèle pour le PDF d'une facture
    function marqueModeleFacture($marque,$modele){
        return mb_strtoupper($marque." - ".$modele);
    }

    // Retourne le montant en euros
    function formatMontantEuros(float $montant){
        return number_format($montant, 2, ',', ' ')." €";
    }

    // Retourne le calcul du total de TVA
    function formatTotalTVA($montantHT,$tauxTVA){
        $tauxTVA = doubleval("0.".$tauxTVA);
        return $montantHT*$tauxTVA;
    }

    // Retourne le calcul du total TTC
    function formatTotalTTC($totalHT,$totalTVA){
        return $totalHT+$totalTVA;
    }

    // Retourne la durée avec l'heure
    function dureeIntervention($duree){
        if($duree < 10 && $duree > 0){
            $dureeHeure = "0".$duree."h";
        }
        elseif($duree >= 10) {
            $dureeHeure = $duree."h";
        }
        return $dureeHeure;
    }

    // Retourne la date fournie en format français
    function dateEnFrancais(DateTime $date){
        return $date->format('d/m/Y');
    }

    // Affiche le nom de la page dans le titre principal
    function titrePage(string $page) {
        // Scinde la chaine en tableau avec un "_" pour récupérer
        // "facture" de "facture_index" par exemple
        $explode = explode("_", $page)[0];
        // Si la valeur à l'index zéro fait partie du tableau, on la renvoie
        if(in_array($explode, $this->listeMenus)) {
            return mb_strtoupper($explode."s");
        }
    }

    // Met en surbrillance le lien du menu qui correspond au slug de la route actuelle
    function menuActif(string $page, string $menu) {
        // Scinde la chaine en tableau avec un "_" pour récupérer
        // "facture" de "facture_index" par exemple
        $explode = explode("_", $page)[0];
        // Si la valeur à l'index zéro fait partie du tableau, on met en surbrillance
        if($explode === $menu) {
            return "active";
        }
        else {
            return "text-dark";
        }
    }
}
