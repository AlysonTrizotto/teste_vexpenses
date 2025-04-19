<?php

namespace Tests;

use App\Models\v1\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    
    /**
     * Cria um usuário fake e autenticado.
     *
     * @return User
     */
    public function createAuthenticatedUser(): User
    {
        $user = User::factory()->create();

        return $user;
    }

    /**
     * Autentica um usuário (via Sanctum) e retorna um token de acesso.
     *
     * @param User $user
     * @return string
     */
    public function authenticateAndGetToken(User $user): string
    {
        $credentials = [
            'email' => $user->email,
            'password' => 'password',
        ];
        
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return "";
        }

        return $token;
    }
}
