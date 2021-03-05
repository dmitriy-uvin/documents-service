<?php

namespace App\Actions\User;

class AddUserRequest
{
    private string $name;
    private string $email;
    private string $password;
    private string $department;
    private int $roleId;

    public function __construct(
        string $name,
        string $email,
        string $password,
        string $department,
        int $roleId
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->department = $department;
        $this->roleId = $roleId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDepartment(): string
    {
        return $this->department;
    }

    public function getRoleId(): int
    {
        return $this->roleId;
    }
}
