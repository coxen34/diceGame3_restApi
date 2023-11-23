<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Http\Controllers\UserController;
use App\Http\Controllers\GameController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use App\Models\User;
use App\Models\Game;

use Database\Seeders;
use Database\Seeders\UserTableSeeder;
use Illuminate\Support\Facades\DB;



class UserTest extends TestCase
{

    use DatabaseTransactions;


    public function test_register()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->accessToken;

        $data = [
            'name' => 'TestUser',
            'email' => 'testuser@example.com',
            'password' => 'TestPassword123!',
        ];

        $response = $this->withToken($token)->post('/api/register', $data);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'TestUser',
                'email' => 'testuser@example.com',
            ]);

        $this->assertDatabaseHas('users', ['name' => 'TestUser', 'email' => 'testuser@example.com']);
    }

    public function test_login_with_correct_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $this->assertArrayHasKey('token', $response->json());
    }

    public function logout()
    {
        $user = Auth::user();
        if ($user) {
            $user->tokens->each->revoke();
            return response()->json(['message' => 'Cierre de sesión satisfactorio'], 200);
        } else {
            return response()->json(['message' => 'Usuario no autentificado'], 401);
        }
    }

    public function test_update()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->accessToken;

        $data = [
            'name' => 'UpdatedUser',
        ];

        $response = $this->withToken($token)->put('/api/players/' . $user->id, $data);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'UpdatedUser',
            ]);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'UpdatedUser']);
    }


    public function testGetWorstPlayer()
    {
        
        $user1 = User::factory()
            ->has(Game::factory()->state(['won'=>false])->count(3), 'games')
            ->create();
            dd('USER1' . $user1);
        $user2 = User::factory()
            ->has(Game::factory()->state(['won'=>true])->count(3), 'games')
            ->create();


      
        $response = $this->actingAs($user1)->get('/api/players/ranking/loser');
        // dd($response);
        
        $response->assertStatus(200);

        
        $worstPlayer = $response->json();

        $this->assertEquals($user1->id, $worstPlayer['user']['id']);
    }
}
