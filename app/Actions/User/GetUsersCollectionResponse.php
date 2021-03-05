<?php

namespace App\Actions\User;

use Illuminate\Support\Collection;

class GetUsersCollectionResponse
{
    private Collection $users;

    public function __construct(Collection $users)
    {
        $this->users = $users;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }
}
