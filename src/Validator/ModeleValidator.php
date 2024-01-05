<?php

namespace App\Validator;

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

    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Modele $constraint */

        // Récupère les données du formulaires
        $modele = $this->context->getObject()->getParent()->getData();

        // Si le véhicule est déjà dans une intervention, la modification du client, marque et modèle est désactivé.
        // Étant donné que si ces champs sont désactivés, ils ne sont pas envoyés dans les données du formulaire.
        // Alors la vérification du modèle de la marque n'est pas prise en compte
        if ($modele->getFkClient() !== null && $modele->getFkModele() !== null) {
            if ('' === $value) {
                return $this->context->buildViolation("Veuillez sélectionner un modèle de la marque.")
                    ->addViolation();
            }
            // Si aucun modèle n'est associé à l'identifiant de la marque, on génère une erreur
            if(empty($this->repository->findBy(['modele' => $value->getModele()]))) {
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ marque }}', $value->getFkMarque()->getMarque())
                    ->setParameter('{{ modele }}', $value->getModele())
                    ->addViolation();
            }
        }
    }
}
