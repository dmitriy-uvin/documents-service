<?php

namespace Database\Seeders;

use App\Constants\Roles;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'Главный',
            'second_name' => 'Администратор',
            'patronymic' => '',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'unhashed_password' => '123123123',
            'department' => 'admins'
        ]);
        $role = Role::where('alias', '=', Roles::ADMINISTRATOR_ALIAS)->get()->first();
        $user->role()->attach($role);
    }
}
