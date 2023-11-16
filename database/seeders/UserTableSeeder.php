<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::findByName('admin');
        $role1 = Role::findByName('player');

        User::create([
            'name' => 'admin',
            'email' => 'admin@mailto.com',
            'password' => Hash::make('admin'),
        ])->assignRole($role);
        User::create([
            'name' => 'Carla',
            'email' => 'carla@mailto.com',
            'password' => Hash::make('5**A__55446644'),
        ])->assignRole($role1);
        User::create([
            'name' => 'Jana',
            'email' => 'jana@mailto.com',
            'password' => Hash::make('5**A__55446644'),
        ])->assignRole($role1);
        User::create([
            'name' => 'player',
            'email' => 'player@mailto.com',
            'password' => Hash::make('player'),
        ])->assignRole($role1);
    }
}
