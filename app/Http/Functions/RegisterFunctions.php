<?php

namespace App\Http\Functions;


use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class RegisterFunctions{

public function validateRegistrationData(Request $request)
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

public function generateName(Request $request)
{
    $name = $request->name;
    if ($name == NULL) {
        $name = 'ANONYMOUS';
    } else {
        $user = User::where('name', $name)->first();
        if ($user) {
            throw ValidationException::withMessages(['name' => 'El nombre ya está en uso.']);
        }
    }
    return $name;
}

public function createUser(Request $request, $name)
{
    // var_dump($request);
    $user = User::create([
        'name' => $name,
        'email' => $request->email,
        'password' => Hash::make($request->password),

    ]);
    // var_dump($user);
    return $user;
}

public function assignRoleToUser($user)
{
    $role = Role::findByName('player');
    $user->assignRole($role);
}
}