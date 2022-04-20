<?php

namespace Rhf\Exceptions;

class FitnessUnauthorisedException extends FitnessHttpException
{
    /**
     * FitnessBadRequestException constructor.
     * @param string $message
     * @param null $errors
     */
    public function __construct($message, $errors = null)
    {
        parent::__construct($message, 401, $errors);
    }
}
