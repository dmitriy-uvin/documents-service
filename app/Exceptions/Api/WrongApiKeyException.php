<?php

namespace App\Exceptions\Api;

class WrongApiKeyException extends \DomainException
{
    protected $message = "Неверный API KEY";
}
