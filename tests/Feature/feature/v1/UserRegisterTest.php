<?php

namespace Tests\Feature\feature\v1;

use App\Models\v1\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    /**
     * Test that a user can register using factory data and a plain password.
     *
     * It tests that:
     *  1. The user can register with a plain password and factory data;
     *  2. The response has a status of 201 Created;
     *  3. The response has a successful status and a message;
     *  4. The response has a JSON structure with the user's data;
     *  5. The user is actually registered in the database.
     *
     * @return void
     */
    public function test_user_can_register_using_factory_data_and_plain_password()
    {
        $plainPassword = 'MySecurePassword123!';
        $fakeUser = User::factory()->make();

        $payload = [
            'name' => $fakeUser->name,
            'email' => $fakeUser->email,
            'birth_date' => $fakeUser->birth_date,
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/v1/user/register', $payload);

        $response->assertCreated()
            ->assertJson([
                'status' => true,
                'message' => 'Success on register user',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'birth_date',
                    'created_at',
                    'updated_at',
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $fakeUser->email,
        ]);
    }
}
