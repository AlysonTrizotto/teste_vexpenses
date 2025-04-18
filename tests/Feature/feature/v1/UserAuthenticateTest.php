<?php

namespace Tests\Feature\feature\v1;

use App\Models\v1\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAuthenticateTest extends TestCase
{
    /**
     * Test that a user can authenticate with a plain password and a factory user.
     *
     * @return void
     */
    public function test_user_can_authenticate_with_plain_password_and_factory_user()
    {
        $plainPassword = 'MySecurePassword123!';
        $cryptPassword = bcrypt($plainPassword);
        $user = User::factory()->create([
            'password' => $cryptPassword,
        ]);

        $payload = [
            'email' => $user->email,
            'password' => $plainPassword,
        ];

        $response = $this->withHeaders([
            'Content-Type' => 'application/json',
        ])->postJson('/api/v1/user/authenticate', $payload);

        $response->assertCreated()
            ->assertJson([
                'status' => true,
                'message' => 'Success on login user',
            ])
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'refresh_token',
                    'time_to_expire_token',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ]
                ]
            ]);
    }
}
