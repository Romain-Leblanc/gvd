<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CodePostalValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\CodePostal $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // Longueur maximale et composé uniquement de 5 chiffres.
        if (!preg_match('/^[0-9]{5}$/', $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
