<?php

namespace Tests\Feature\v1;

use App\Models\v1\Log;
use App\Models\v1\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogTest extends TestCase
{
    public function test_get_log_by_id()
    {
        $user = $this->createAuthenticatedUser();
        $token = $this->authenticateAndGetToken($user);

        $log = Log::factory()->create();
        $userLog = User::findOrFail($log->user_id);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/log/{$log->id}");

        
        $response->assertOk();

        
        $response->assertJson([
            'status' => true,
            'message' => 'Log recuperado com sucesso',
            'data' => [
                'id' => $log->id,
                'action' => $log->action,
                'description' => $log->description,
                'user_id' => $log->user_id,
                'user' => [
                    'id' => $userLog->id,
                    'name' => $userLog->name,
                    'email' => $userLog->email,
                    'birth_date' => \Carbon\Carbon::parse($userLog->birth_date)->toDateString(),
                ],
            ],
        ]);
    }

    public function test_get_logs()
    {
        $user = $this->createAuthenticatedUser();
        $token = $this->authenticateAndGetToken($user);

        Log::factory(10)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/log");

        $response->assertOk();        
        $response->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'action',
                        'description',
                        'user_id',
                        'created_at',
                        'updated_at',
                        'user' => [
                            'id',
                            'name',
                            'email',
                            'birth_date',
                            'created_at',
                            'updated_at',
                        ]
                    ]
                ],
                'first_page_url',
                'from',
                'last_page',
                'last_page_url',
                'links' => [
                    '*' => [
                        'url',
                        'label',
                        'active',
                    ]
                ],
                'next_page_url',
                'path',
                'per_page',
                'prev_page_url',
                'to',
                'total',
            ],
        ]);
    }

}
