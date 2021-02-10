<?php

namespace App\Exceptions\User;

use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserAlreadyExistsException extends \DomainException
{
    protected $message = 'Пользователь уже существует!';
}
