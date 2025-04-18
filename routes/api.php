<?php

use App\Enums\v1\TokenAbility;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function(){
    Route::prefix('user')->group(function(){
        Route::middleware('guest')->group(function () {
            // Authentication and Register
            Route::post('register', [App\Http\Controllers\API\v1\Authentication\RegisterController::class, 'register']);
            Route::post('authenticate', [App\Http\Controllers\API\v1\Authentication\AuthenticationController::class, 'authenticate'])->name('login');
            

            // Password Reset
            Route::post('forgot-password', [App\Http\Controllers\API\v1\Authentication\PasswordResetLinkController::class, 'store'])
                ->name('password.email');
            Route::post('recover-password', [App\Http\Controllers\API\v1\Authentication\NewPasswordController::class, 'store'])
                ->name('password.reset');
        });



        Route::middleware(['auth:sanctum',])->group(function () {
            Route::middleware(['ability:' . TokenAbility::ISSUE_ACCESS_TOKEN->value])
                ->get('refresh-token', [App\Http\Controllers\API\v1\Authentication\AuthenticationController::class, 'refreshToken']);

            Route::get('me', [App\Http\Controllers\API\v1\UserController::class, 'me']);

            Route::get('/logout', [App\Http\Controllers\API\v1\Authentication\AuthenticationController::class, 'destroy'])
                ->name('logout');

            Route::get('/', [App\Http\Controllers\API\v1\UserController::class, 'index']);
        
            Route::post('/upload', [App\Http\Controllers\API\v1\FileMenagementController::class, 'importUsersFromFile']);
            Route::get('/import-status/{id}', [App\Http\Controllers\API\v1\FileMenagementController::class, 'getImportProgress']);
        });    
        
    });
});