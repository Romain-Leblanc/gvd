<?php

namespace App\Validator\Admin;

use App\Repository\MarqueRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MarqueValidator extends ConstraintValidator
{
    private $repository;

    public function __construct(MarqueRepository $marqueRepository)
    {
        $this->repository = $marqueRepository;
    }

    /**
     * Supprime les accents d'une chaine passée en paramètre
     */
    private function remplacerAccents(string $chaine){
        $chaine = trim(str_replace(" ", "", $chaine));
        $recherche  = ['À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'à', 'á', 'â', 'ã', 'ä', 'å', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ò', 'ó', 'ô', 'õ', 'ö', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ'];
        $remplacer = ['A', 'A', 'A', 'A', 'A', 'A', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 'a', 'a', 'a', 'a', 'a', 'a', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y'];

        $chaine  = strtoupper(str_replace($recherche, $remplacer, $chaine));
        return $chaine;
    }
    
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Admin\Marque $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // Supprime les accents de la marque saisie
        $value = $this->remplacerAccents($value);
        // Récupère l'objet Marque du formulaire
        $marque = $this->context->getObject()->getParent()->getData();
        // Récupère un résultat si une marque existe déjà pour la marque saisie
        $marqueExistante = $this->repository->findOneBy(['marque' => $value]);

        // Si l'objet Marque contient un identifiant (modification)
        if($marque->getId() !== null) {
            // Si une marque existe déjà, que son identifiant est différent de celui de la marque modifiée
            // ou que celle modifiée ne contient pas que des lettres, on génère une erreur
            if(!empty($marqueExistante) && $marqueExistante->getId() !== $marque->getId()) {
                $this->context->buildViolation($constraint->message_existe)
                    ->setParameter('{{ marque }}', $value)
                    ->addViolation();
            }
            elseif (!ctype_alpha($value)) {
                $this->context->buildViolation($constraint->message_lettre)
                    ->setParameter('{{ marque }}', $value)
                    ->addViolation();
            }
        }
        // Si l'objet Marque ne contient pas d'identifiant (ajout)
        else {
            // Si une marque existe déjà ou que celle ajoutée ne contient pas que des lettres,
            // on génère une erreur
            if(!empty($marqueExistante)) {
                $this->context->buildViolation($constraint->message_existe)
                    ->setParameter('{{ marque }}', $value)
                    ->addViolation();
            }
            elseif (!ctype_alpha($value)) {
                $this->context->buildViolation($constraint->message_lettre)
                    ->setParameter('{{ marque }}', $value)
                    ->addViolation();
            }
        }
    }
}
