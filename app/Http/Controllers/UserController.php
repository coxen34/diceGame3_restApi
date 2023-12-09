<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\OAuthServerException;
use App\Http\Functions\RegisterFunctions;
use App\Http\Functions\LoginFunctions;
use App\Http\Functions\UpdateFunctions;



class UserController extends Controller
{
    protected $registerFunctions;
    protected $loginFunctions;
    protected $updateFunctions;

    public function __construct(RegisterFunctions $registerFunctions,LoginFunctions $loginFunctions,UpdateFunctions $updateFunctions)
    {
        $this->registerFunctions = $registerFunctions;
        $this->loginFunctions = $loginFunctions;
        $this->updateFunctions = $updateFunctions;
    }
    

    /**
     * ----------REGISTRO--------------
     */
    public function register(Request $request)
    {
        try {
            $this->registerFunctions->validateRegistrationData($request);

            $name = $this->registerFunctions->generateName($request);

            $user = $this->registerFunctions->createUser($request, $name);


            $this->registerFunctions->assignRoleToUser($user);

            return response()->json($user, 201);
        } catch (ValidationException $e) {

            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Se ha producido un error al crear usuari@.'], 500);
        }
    }
    
    /**
     * --------------- LOGIN--------------
     */
    public function login(Request $request)
    {
        try {

            $validateData = $this->loginFunctions->validateDataLogin($request);

            $credentials = [
                'email' => $validateData['email'],
                'password' => $validateData['password']
            ];


            if (Auth::attempt($credentials)) {

                $user = $request->user();

                $token = $user->createToken('example')->accessToken;

                return response()->json([
                    'message' => 'Inicio de sesión correcto',
                    'user' => $user,
                    'token' => $token
                ], 200);
            } else {
                return response()->json(['error' => 'Credenciales incorrectas.'], 401);
            }
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (OAuthServerException $e) {
            return response()->json(['error' => 'Error de autenticación.'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Se ha producido un error durante el inicio de sesión.' . $e->getMessage()], 500);
        }
    }

     //LOGOUT
    public function logout()
    {
        $user = Auth::user();

        if ($user) {

            $user->tokens->each->revoke();

            return response()->json('Cierre de sesión satisfactorio', 200);
        } else {
            return response()->json('Usuario no autentificado', 401);
        }
    }
    //UPDATE NAME
    public function update(Request $request, $id)
    {
        try {

            if (!auth()->check()) {
                return response()->json(['error' => 'Usuario no autenticado.'], 401);
            }

            if (auth()->user()->id != $id) {
                return response()->json(['error' => 'No tienes permiso para actualizar este usuario.'], 403);
            }

            // $this->validateRequestUpdate($request, $id);
            $this->updateFunctions->validateRequestUpdate($request,$id);

            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado.'], 404);
            }

            $name = $this->registerFunctions->generateName($request);
            $user->name = $name;

            $user->save();

            return response()->json($user, 200);
        } catch (ValidationException $e) {

            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Ocurrió un error al actualizar el usuario.'], 500);
        }
    }
    /**
     * ----------BLOQUE MOSTRAR JUGADOR CON % DE EXITOS--------------
     */

    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();
        $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);

        return response()->json([$usersWithSuccessPercentage], 200);
    }

    public function calculateSuccessPercentage($users)
    {
        $result = $users->map(function ($user) {
            $totalGames = $user->games->count();
            $wonGames = $user->games->where('won', true)->count();

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'success_percentage' => $totalGames > 0 ? ($wonGames / $totalGames) * 100 : 0,
            ];
        });

        return $result;
    }


    /**
     * ---------------FIN BLOQUE JUGADOR CON % DE EXITOS--------------
     */


    /**
     * ------------rànquing mitjà de tots els jugadors/es del sistema. És a dir, el percentatge mitjà d’èxits.
     */

    public function getPlayersRanking()
    {

        $users = User::all();
        if ($users->isEmpty()) {
            return response()->json(['error' => 'No hay jugadores en el sistema'], 404);
        }
        $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);

        $sortedUsers = $usersWithSuccessPercentage->sortByDesc('success_percentage');

        $sortedUsers = $sortedUsers->values();
        return response()->json($sortedUsers, 200);
    }


   

    /**
     * ------------FIN rànquing mitjà de tots els jugadors/es del sistema. És a dir, el percentatge mitjà d’èxits.
     */

    public function getWorstPlayer()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return response()->json(['error' => 'No hay jugadores en el sistema'], 404);
        }

        $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);


        $sortedUsers = $usersWithSuccessPercentage->sortBy('success_percentage');


        $worstPlayer = $sortedUsers->first();

        return response()->json($worstPlayer, 200);
    }

    public function getBestPlayer()
    {
        $users = User::all();

        if ($users->isEmpty()) {
            return response()->json(['error' => 'No hay jugadores en el sistema'], 404);
        }

        $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);


        $sortedUsers = $usersWithSuccessPercentage->sortByDesc('success_percentage');


        $bestPlayer = $sortedUsers->first();

        return response()->json($bestPlayer, 200);
    }
}
