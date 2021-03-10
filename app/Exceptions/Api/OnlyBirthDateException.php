<?php

namespace App\Exceptions\Api;

use App\Exceptions\BaseException;

class OnlyBirthDateException extends BaseException
{
    public $message = "Вы не можете искать только по дате рождения!";
}
