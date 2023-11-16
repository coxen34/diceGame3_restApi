<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;



class GamesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Game::create([
            'dice1' => 4, 'dice2' => 6,
            'won' => false,
            'user_id' => 1, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Game::create([
            'dice1' => 1, 'dice2' => 6,
            'won' => true,
            'user_id' => 1, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Game::create([
            'dice1' => 5, 'dice2' => 6,
            'won' => false,
            'user_id' => 2, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Game::create([
            'dice1' => 4, 'dice2' => 6,
            'won' => false,
            'user_id' => 2, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Game::create([
            'dice1' => 5, 'dice2' => 2,
            'won' => true,
            'user_id' => 3, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Game::create([
            'dice1' => 5, 'dice2' => 5,
            'won' => false,
            'user_id' => 3, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Game::create([
            'dice1' => 1, 'dice2' => 1,
            'won' => false,
            'user_id' => 4, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        Game::create([
            'dice1' => 2, 'dice2' => 3,
            'won' => false,
            'user_id' => 4, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
