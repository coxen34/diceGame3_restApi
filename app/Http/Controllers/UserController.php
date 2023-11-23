<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use League\OAuth2\Server\Exception\OAuthServerException;






class UserController extends Controller
{
     /**
     * ----------BLOQUE MOSTRAR JUGADOR CON % DE EXITOS--------------
     */

    public function index()
    {
        $users = User::orderBy('name', 'asc')->get();
        $usersWithSuccessPercentage = $this->calculateSuccessPercentage($users);

        return response()->json([$usersWithSuccessPercentage], 200);
    }

    protected function calculateSuccessPercentage($users)
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
         $players = User::all();
     
         if ($players->isEmpty()) {
             return response()->json(['error' => 'No hay jugadores en el sistema'], 404);
         }
     
         $totalSuccessPercentage = 0;
         $totalGamesCount = 0;
     
         foreach ($players as $player) {
             $games = $player->games;
     
             if ($games->isNotEmpty()) {
                 $totalSuccessPercentage += $this->calculateSuccessPercentageAll($games) * $games->count();
                 $totalGamesCount += $games->count();
             }
         }
     
         $averageSuccessPercentage = $totalGamesCount > 0 ? $totalSuccessPercentage / $totalGamesCount : 0;
     
         return response()->json(['% medio de éxitos de todos los jugadores' => $averageSuccessPercentage]);
     }
     
     protected function calculateSuccessPercentageAll($games)
     {
         $totalGames = $games->count();
         $wonGames = $games->where('won', true)->count();
     
         return $totalGames > 0 ? ($wonGames / $totalGames) * 100 : 0;
     }
     
/**
     * ------------FIN rànquing mitjà de tots els jugadors/es del sistema. És a dir, el percentatge mitjà d’èxits.
     */



    /**
     * ----------BLOQUE FUNCIONES REGISTRO--------------
     */
    public function register(Request $request)
    {
        try {
            $this->validateRegistrationData($request);

            $name = $this->generateName($request);

            $user = $this->createUser($request, $name);


            $this->assignRoleToUser($user);

            return response()->json($user, 201);
        } catch (ValidationException $e) {

            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Se ha producido un error al crear usuari@.'], 500);
        }
    }
    private function validateRegistrationData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable',
            'email' => 'required|email|unique:users',
            'password' => 'required|regex:/^(?=.*[A-Z])(?=.*[!@#\$%\^&\*]).{9,}$/'
        ]);

        $validator->setAttributeNames([
            'email' => 'correo electrónico',
            'password' => 'contraseña'
        ]);

        $validator->setCustomMessages([
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El campo :attribute debe ser una dirección de correo válida.',
            'unique' => 'Este :attribute ya está en uso.',
            'regex' => 'La :attribute debe contener al menos una mayúscula y un carácter especial y tener al menos 9 caracteres de longitud.'
        ]);

        if (!empty($request->name)) {
            $existingUser = User::where('name', $request->name)->first();
            if ($existingUser) {
                throw ValidationException::withMessages(['name' => 'El name ya está en uso.']);
            }
        }

        $validator->validate();
    }

    private function generateName(Request $request)
    {
        return $request->name ?: 'anonymous ' . time();
    }

    private function createUser(Request $request)
    {
        // var_dump($request);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);
        // var_dump($user);
        return $user;
    }

    private function assignRoleToUser($user)
    {
        $role = Role::findByName('player');
        $user->assignRole($role);
    }
    /**
     * ---------------FIN BLOQUE REGISTRO--------------
     */

    /**
     * ---------------LOGIN--------------
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $validator->setAttributeNames([
                'email' => 'correo electrónico',
                'password' => 'contraseña',
            ]);

            $validator->setCustomMessages([
                'required' => 'El campo :attribute es obligatorio.',
                'email' => 'El campo :attribute debe ser una dirección de correo válida.',
            ]);

            $validator->validate();

            $credentials = $request->only('email', 'password');


            if (Auth::attempt($credentials)) {
                

                // $user = Auth::user();

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
        // var_dump($user);
        // exit(0);
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

            $validator = Validator::make($request->all(), [
                'name' => 'nullable|unique:users,name,' . $id, 
            ]);

            $validator->setAttributeNames([
                'name' => 'nombre',
            ]);

            $validator->setCustomMessages([
                'unique' => 'Este :attribute ya está en uso.',
            ]);

            $validator->validate();

            $user = User::find($id);

            if (!$user) {
                return response()->json(['error' => 'Usuario no encontrado.'], 404);
            }

            // Asigna "anonymous" como valor por defecto si el campo "name" se deja vacío
            if ($request->has('name')) {
                $user->name = $request->input('name');
            } else {
                $user->name = 'anonymous' . time();
            }
            $user->save();

            return response()->json($user, 200);
        } catch (ValidationException $e) {
            
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // var_dump($e);
            // exit(0);
            return response()->json(['error' => 'Ocurrió un error al actualizar el usuario.'], 500);
        }
    }
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

    // Ordena los usuarios por su porcentaje de éxito de mayor a menor
    $sortedUsers = $usersWithSuccessPercentage->sortByDesc('success_percentage');

    // Obtén al primer usuario de la lista (el que tiene el mejor porcentaje)
    $bestPlayer = $sortedUsers->first();

    return response()->json($bestPlayer, 200);
}


}
