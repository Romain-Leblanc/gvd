<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ImmatriculationValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\Immatriculation $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        // Format de plaque d'immatriculation ancien ou nouveau
        if (!preg_match('/^[A-Z]{2}[-][0-9]{3}[-][A-Z]{2}$/', $value) === true && !preg_match('/^[0-9]{1,4}[-][A-Z]{2}[-][0-9]{2}$/', $value) === true) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
