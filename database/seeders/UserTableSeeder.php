<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );
       
        $role1 = Role::firstOrCreate(
            ['name' => 'player', 'guard_name' => 'web']
        );

        $user1 = User::firstOrCreate(
            [
                'name' => 'admin',
                'email' => 'admin@mailto.com',
                'email_verified_at' => now(), 'password' => Hash::make('admin'),
                'remember_token' => Str::random(10),
            ]
        );
        if (!$user1->hasRole('admin')) {
            $user1->assignRole($role);
        }

        $user2 = User::firstOrCreate([
            'name' => 'Carla',
            'email' => 'carla@mailto.com',
            'email_verified_at' => now(),
            'password' => Hash::make('5**A__55446644'),
            'remember_token' => Str::random(10),
        ]);
        if (!$user2->hasRole('player')) {
            $user2->assignRole($role1);
        }
        
        $user3 = User::firstOrCreate([
            'name' => 'Jana',
            'email' => 'jana@mailto.com',
            'email_verified_at' => now(),
            'password' => Hash::make('5**A__55446644'),
            'remember_token' => Str::random(10),
        ]);
        if (!$user3->hasRole('player')) {
            $user3->assignRole($role1);
        }
        
        $user4 = User::create([
            'name' => 'player',
            'email' => 'player@mailto.com',
            'email_verified_at' => now(),
            'password' => Hash::make('player'),
            'remember_token' => Str::random(10),
        ]);
        if (!$user4->hasRole('player')) {
            $user4->assignRole($role1);
        }
        
    }
}
