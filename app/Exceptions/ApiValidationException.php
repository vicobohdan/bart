<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ApiException;

class ApiValidationException extends ApiException
{
    public function __construct($message)
    {
    	parent::__construct($message, 400);
    }
}
