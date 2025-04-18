<?php

namespace Tests;

use App\Enums\v1\TokenAbility;
use App\Models\v1\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
        return $user->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value, $user->user_type], // abilities
            Carbon::now()->addMinutes(config('sanctum.ac_expiration')) // expiração (Laravel 12+)
        )->plainTextToken;
    }
}
