<?php

namespace App\Validator\Admin;

use App\Repository\ModeleRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ModeleValidator extends ConstraintValidator
{
    private $repository;

    public function __construct(ModeleRepository $modeleRepository)
    {
        $this->repository = $modeleRepository;
    }

    /**
     * Supprime les accents d'une chaine passée en paramètre
     */
    private function remplacerAccents(string $chaine){
        $chaine = trim($chaine);
        $recherche  = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'];
        $remplacer = ['A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y'];

        $chaine  = strtoupper(str_replace($recherche, $remplacer, $chaine));
        return $chaine;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Admin\Modele $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // Supprime les accents du modèle saisie
        $value = $this->remplacerAccents($value);
        // Récupère l'objet Modèle du formulaire
        $modele = $this->context->getObject()->getParent()->getData();
        // Récupère un résultat si un modèle de cette marque existe déjà pour le modèle saisie
        $modeleExistant = $this->repository->findOneBy(['fk_marque' => $modele->getFkMarque()->getId(), 'modele' => $value]);

        // Si l'objet Modèle contient un identifiant (modification)
        if($modele->getId() !== null) {
            // Si un modèle de cette marque existe déjà et que l'identifiant du modèle existant est différent de celui modifié,
            // on génère une erreur
            if(!empty($modeleExistant) && $modeleExistant->getId() !== $modele->getId()) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ modele }}', $value)
                    ->addViolation();
            }
        }
        // Si l'objet Modèle ne contient pas d'identifiant (ajout)
        else {
            // Si un modèle de cette marque existe déjà,
            // on génère une erreur
            if(!empty($modeleExistant)) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ modele }}', $value)
                    ->addViolation();
            }
        }
    }
}
