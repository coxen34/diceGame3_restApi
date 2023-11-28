<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



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

        $response = $this->withToken($token)->post('/api/players', $data);

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
            'password' => Hash::make('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200);

        $this->assertArrayHasKey('token', $response->json());
    }


    public function test_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('api/logout');

        if ($response->status() === 302) {
            $response->assertRedirect('/api/login');
        } else {
            $response->assertStatus(200);
            $response->assertJson([
                'message' => 'Cierre de sesión satisfactorio',
            ]);
        }
    }
    public function test_index()
    {
        $user = User::factory()->create(['name' => 'Test User']);

        $this->actingAs($user);

        $user->assignRole('admin');

        $response = $this->get('api/players/');

        $token = $user->createToken('test-token')->accessToken;

        $response = $this->withToken($token)->getJson('/api/players');

        $response->assertStatus(200);
    }

    public function test_update()
    {
        $user = User::factory()->create();

        $token = $user->createToken('TestToken')->accessToken;

        $user->assignRole('player');
        
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


    //Funciones helper
    private function getUser($name)
    {
        $user = User::where('name', $name)->first();
        if (!$user) {
            $user = User::factory()->create(['name' => $name]);
        }
        return $user;
    }

    private function assertResponseWorstPlayer($response)
    {
        if ($response->status() === 302) {
            $response->assertRedirect('/api/login');
        } else {
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'user' => [
                    'id',
                    'name'
                ],
                'success_percentage'
            ]);
        }
    }

    private function assertResponseBestPlayer($response)
    {
        if ($response->status() === 302) {
            $response->assertRedirect('/api/login');
        } else {
            $response->assertStatus(200);
            $response->assertJsonStructure([
                'user' => [
                    'id',
                    'name'
                ],
                'success_percentage'
            ]);
        }
    }
    //Fin funciones helper

    public function testGetWorstPlayer()
    {

        $user = $this->getUser('admin');

        $this->actingAs($user);

        $response = $this->get('api/players/ranking/loser');

        $this->assertResponseWorstPlayer($response);
    }

    public function testGetBestPlayer()
    {

        $user = $this->getUser('player');

        $this->actingAs($user);

        $response = $this->get('api/players/ranking/winner');

        $this->assertResponseBestPlayer($response);
    }
}
