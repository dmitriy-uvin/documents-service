<?php

namespace App\Repositories\User;

use App\Contracts\EloquentCriterion;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class UserRepository implements UserRepositoryInterface
{
    public function save(User $user): User
    {
        $user->save();
        return $user;
    }

    public function attachRole(User $user, Role $role): User
    {
        $user->role()->attach($role);
        return $user;
    }

    public function findByCriteria(EloquentCriterion ...$criteria): Collection
    {
        $query = User::query();

        foreach ($criteria as $criterion) {
            $query = $criterion->apply($query);
        }

        return $query->get();
    }

    public function getAll(): Collection
    {
        return User::all();
    }

    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function deleteById(int $id): void
    {
        User::destroy($id);
    }

    public function me(): User
    {
        return Auth::user();
    }
}
