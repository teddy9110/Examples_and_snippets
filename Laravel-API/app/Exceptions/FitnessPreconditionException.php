<?php

namespace Rhf\Exceptions;

class FitnessPreconditionException extends FitnessHttpException
{
    /**
     * FitnessPreconditionException constructor.
     * @param string $message
     * @param null $errors
     */
    public function __construct($message, $errors = null)
    {
        parent::__construct($message, 412, $errors);
    }
}
