<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ApiException;

class ApiNotFoundException extends ApiException
{
    public function __construct()
	{
		parent::__construct('Resource not found', 404);
	}
}
