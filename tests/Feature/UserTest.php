<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Passport\Passport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
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
            'password' => Hash::make('password'),
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
