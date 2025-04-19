<?php

namespace Tests\Feature\v1;

use Tests\TestCase;

class GetAuthenticatedUserTest extends TestCase
{
    /**
     * Verifica se o endpoint /api/v1/user/me retorna o usuario autenticado com a estrutura esperada.
     *
     */
    public function test_get_authenticated_user_returns_expected_structure()
    {
        $user = $this->createAuthenticatedUser();
        $token = $this->authenticateAndGetToken($user);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user/me');

        
        $response->assertOk()
            ->assertJson([
                'status' => true,
                'message' => 'Success on get user',
                'data' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'birth_date' => $user->birth_date,
                ]
            ]);
    }
}
