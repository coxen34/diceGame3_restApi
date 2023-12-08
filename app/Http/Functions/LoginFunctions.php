<?php

namespace App\Http\Functions;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LoginFunctions
{
    public function validateDataLogin(Request $request)
    {
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

        return $validator->validate();
    }
}
