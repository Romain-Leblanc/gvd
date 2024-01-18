<?php

namespace App\Validator\Admin;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Marque extends Constraint
{
    public $message_lettre = "La marque doit contenir uniquement des lettres.";
    public $message_existe = "Cette marque existe déjà.";
}
