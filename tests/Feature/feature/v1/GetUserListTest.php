<?php

namespace Tests\Feature\feature\v1;

use App\Models\v1\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUserListTest extends TestCase
{
    /**
     * Verifica se o endpoint /api/v1/user retorna a lista de usu rios
     * paginada, com a estrutura esperada.
     *
     * @test
     */
    public function test_it_returns_paginated_users_list_with_expected_structure(): void
    {
        $user = $this->createAuthenticatedUser();
        $token = $this->authenticateAndGetToken($user);

        
        User::factory()->count(50)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/user');

    
        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'current_page',
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'birth_date',
                        ],
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ]);

        
        $json = $response->json();

        $this->assertEquals(true, $json['status']);
        $this->assertEquals('Success on get users', $json['message']);
        $this->assertCount(10, $json['data']['data']);
    }
}
