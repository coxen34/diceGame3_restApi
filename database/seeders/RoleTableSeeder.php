<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role1 = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'player']);

        //Permissions admin
        Permission::create(['name'=>'games.getPlayerGames'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'users.index'])->syncRoles([$role1,$role2]);
        Permission::create(['name'=>'games.players.ranking'])->assignRole($role1);

        //Permissions player
       /*  Permission::create(['name'=>'games.getPlayerGames']);
        Permission::create(['name'=>'users.index']); */
       
    }
}
