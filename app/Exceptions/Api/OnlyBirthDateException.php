<?php

namespace App\Exceptions\Api;

use App\Exceptions\BaseException;

class OnlyBirthDateException extends \DomainException
{
    public $message = "Вы не можете искать только по дате рождения!";
}
