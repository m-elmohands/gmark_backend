<?php

return [
    'required' => 'The :attribute field is required.',
    'string' => 'The :attribute must be a string.',
    'email' => 'The :attribute must be a valid email address.',
    'max' => [
        'string' => 'The :attribute may not be greater than :max characters.',
    ],
    'boolean' => 'The :attribute field must be true or false.',
    'lowercase' => 'The :attribute must be lowercase.',
    'unique' => 'The :attribute has already been taken.',
    'confirmed' => 'The :attribute confirmation does not match.',
    'current_password' => 'The current password is incorrect.',
    'different' => 'The :attribute and :other must be different.',
    'password' => [
        'letters' => 'The :attribute must contain at least one letter.',
        'numbers' => 'The :attribute must contain at least one number.',
    ],
    'attributes' => [
        'name' => 'name',
        'email' => 'email address',
        'password' => 'password',
        'password_confirmation' => 'password confirmation',
        'remember' => 'remember me option',
        'token' => 'reset token',
        'current_password' => 'current password',
    ],
];
