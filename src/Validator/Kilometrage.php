<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Kilometrage extends Constraint
{
    public $message_valeur = "Le champ 'Kilométrage' doit être compris entre 1 km et 2 millions.";
    public $message_virgule = "Le champ 'Kilométrage' ne doit pas contenir de virgules";
}
