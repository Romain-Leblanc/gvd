<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class NumTel extends Constraint
{
    public $message = "Le champ du n° téléphone doit commencé par un 0, contenir aucun blanc et une longueur maximale de 10 chiffres.";
}
