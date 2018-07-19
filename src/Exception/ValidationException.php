<?php

namespace App\Exception;

/**
 * Class ValidationException
 * @package App\Exception
 */
class ValidationException extends \Exception
{
    /**
     * ValidationException constructor.
     * @param string $message
     */
    public function __construct(string $message = '')
    {
        parent::__construct($message, 400);
    }
}