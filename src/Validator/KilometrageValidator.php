<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class KilometrageValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Kilometrage $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // Si la valeur n'est pas comprise entre 1 et 2 millions de kms, on génère une erreur
        if($value < 1 || $value > 2000000){
            $this->context->buildViolation($constraint->message_valeur)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
