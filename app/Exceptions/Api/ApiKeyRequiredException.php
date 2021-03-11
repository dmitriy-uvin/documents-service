<?php

namespace App\Exceptions\Api;

class ApiKeyRequiredException extends \DomainException
{
    protected $message = "API KEY обязателен!";
}
