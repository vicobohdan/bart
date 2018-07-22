<?php

namespace App\Exceptions;

use Exception;
use App\Exceptions\ApiException;

class ApiGalleryExistException extends ApiException
{
    public function __construct()
    {
    	parent::__construct('Gallery already exists.', 409);
    }
}
