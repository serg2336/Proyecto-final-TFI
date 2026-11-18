<?php

return [
    'required' => 'El campo :attribute es obligatorio.',
    'email' => 'El campo :attribute debe ser un correo electrónico válido.',
    'unique' => 'El valor del campo :attribute ya está en uso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'string' => 'El campo :attribute debe ser texto.',
    'lowercase' => 'El campo :attribute debe estar en minúsculas.',
    'max' => [
        'string' => 'El campo :attribute no puede superar los :max caracteres.',
    ],
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'password' => [
        'letters' => 'La :attribute debe contener al menos una letra.',
        'mixed' => 'La :attribute debe contener al menos una mayúscula y una minúscula.',
        'numbers' => 'La :attribute debe contener al menos un número.',
        'symbols' => 'La :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'La :attribute apareció en una filtración de datos. Elegí otra.',
    ],
    'attributes' => [
        'name' => 'nombre',
        'email' => 'correo electrónico',
        'password' => 'contraseña',
        'password_confirmation' => 'confirmación de contraseña',
    ],
];
