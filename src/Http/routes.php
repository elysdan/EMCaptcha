<?php

use Elysdan\EMCaptcha\Http\Controllers\CaptchaController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/captcha/emcaptcha', [CaptchaController::class, 'show'])
        ->name('emcaptcha.show');

    Route::get('/captcha/emcaptcha/refresh', [CaptchaController::class, 'refresh'])
        ->name('emcaptcha.refresh');
});
