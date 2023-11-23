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

        // $role1 = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $role1 = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web']
        );
        /*  $role2 = Role::create(['name' => 'player']);
        $role2 = Role::findByName('player', 'web');

        if (!$role2) {
            $role2 = Role::create(['name' => 'player', 'guard_name' => 'web']);
        } */
        $role2 = Role::firstOrCreate(
            ['name' => 'player', 'guard_name' => 'web']
        );

        //Permissions admin
        // Permission::create(['name' => 'games.getPlayerGames'])->syncRoles([$role1, $role2]);
        $permission = Permission::firstOrCreate(['name'=>'games.getPlayerGames', 'guard_name'=>'web']);

        $permission->syncRoles([$role1,$role2]);

        // Permission::create(['name' => 'users.index'])->syncRoles([$role1, $role2]);
        $permission2 = Permission::firstOrCreate(['name'=>'users.index', 'guard_name'=>'web']);

        $permission2->syncRoles([$role1,$role2]);

        // Permission::create(['name' => 'games.players.ranking'])->assignRole($role1);

        $permission3 = Permission::firstOrCreate(['name'=>'games.players.ranking', 'guard_name'=>'web']);

        $permission3->syncRoles([$role1]);

        //Permissions player
        /*  Permission::create(['name'=>'games.getPlayerGames']);
        Permission::create(['name'=>'users.index']); */
    }
}
