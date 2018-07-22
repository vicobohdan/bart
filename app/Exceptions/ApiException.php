<?php

namespace App\Exceptions;

use Exception;

class ApiException extends Exception
{
	protected $description;

    public function __construct($message, $code)
    {
    	parent::__construct("", $code);

    	$this->description = $message;
    }

    public function getDescription()
    {
    	return $this->description;
    }
}
