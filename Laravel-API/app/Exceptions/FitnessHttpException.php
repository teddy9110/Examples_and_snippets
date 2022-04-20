<?php

namespace Rhf\Exceptions;

use Illuminate\Support\MessageBag;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FitnessHttpException extends HttpException
{
    /**
     * MessageBag errors.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * Create a new resource exception instance.
     *
     * @param string                               $message
     * @param int                                  $code
     * @param mixed $errors
     *
     * @return void
     */
    public function __construct($message = null, $code = 0, $errors = null)
    {
        if (is_null($errors)) {
            $this->errors = new MessageBag();
        } else {
            $this->errors = new MessageBag(is_array($errors) ? $errors : [$errors]);
        }

        parent::__construct($code, $message);
    }

    /**
     * Get the errors message bag.
     *
     * @return \Illuminate\Support\MessageBag
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Determine if message bag has any errors.
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !$this->errors->isEmpty();
    }
}
