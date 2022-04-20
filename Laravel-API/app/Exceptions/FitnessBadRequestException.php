<?php

namespace Rhf\Exceptions;

class FitnessBadRequestException extends FitnessHttpException
{
    /**
     * FitnessBadRequestException constructor.
     * @param string $message
     * @param null $errors
     */
    public function __construct($message, $errors = null)
    {
        parent::__construct($message, 400, $errors);
    }
}
