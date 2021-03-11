<?php

namespace App\Exceptions\Api;

class ApiKeyAlreadyExistsException extends \DomainException
{
    protected $message = "API KEY уже существует!";
}
