<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Difficulty Level
    |--------------------------------------------------------------------------
    |
    | Controls the range of numbers used in captcha operations.
    | Options: 'easy' (1-9), 'medium' (10-99), 'hard' (100-999)
    |
    */
    'difficulty' => 'medium',

    /*
    |--------------------------------------------------------------------------
    | Enabled Operations
    |--------------------------------------------------------------------------
    |
    | The arithmetic operations available for captcha generation.
    | Supported: '+', '-', '*'
    |
    */
    'operations' => ['+', '-', '*'],

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    */
    'image' => [
        'width'      => 200,
        'height'     => 70,
        'font_size'  => 28,
        'bg_color'   => '#ffffff',
        'text_color' => '#333333',
        'noise_lines' => 5,
        'noise_dots'  => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    |
    | The session key used to store the captcha answer.
    |
    */
    'session_key' => 'emcaptcha_answer',

    /*
    |--------------------------------------------------------------------------
    | Expiration
    |--------------------------------------------------------------------------
    |
    | Number of minutes before the captcha expires.
    |
    */
    'expire_minutes' => 5,

];
