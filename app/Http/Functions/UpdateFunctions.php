<?php

namespace App\Http\Functions;

use Illuminate\Support\Facades\Validator;

class UpdateFunctions
{
    public function validateRequestUpdate($request, $id)
    {
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
    }

}