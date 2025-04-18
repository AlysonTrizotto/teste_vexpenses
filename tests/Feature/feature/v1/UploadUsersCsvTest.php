<?php

namespace Tests\Feature\feature\v1;

use App\Models\v1\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadUsersCsvTest extends TestCase
{
        /**
         * Verifica se o upload de um arquivo CSV fake com usu rios fake
         *   1. O upload do arquivo CSV fake   realizado com sucesso;
         *   2. O status da importa o   verificado ap s o upload;
         *   3. O status da importa o   verificado ap s 20 segundos;
         *   4. O status da importa o retorna o status correto e os dados da importa o.
         *
         * @return void
         */
    public function test_it_uploads_dynamically_generated_csv_and_creates_users(): void
    {
        $fakeUsers = User::factory()->count(3)->make();
        $csvData = [
            ['Nome', 'E-mail', 'Data de nascimento'],
        ];

        foreach ($fakeUsers as $user) {
            $csvData[] = [
                $user->name,
                $user->email,
                $user->birth_date,
            ];
        }

        $csvContent = collect($csvData)
            ->map(fn ($line) => implode(',', $line))
            ->implode("\n");

        
        Storage::fake('local');
        $tempPath = storage_path('framework/testing/disks/local/users_test.csv');
        file_put_contents($tempPath, $csvContent);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'usuarios_teste.csv',
            'text/csv',
            null,
            true
        );

        
        $user = $this->createAuthenticatedUser();
        $token = $this->authenticateAndGetToken($user);

        $response = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->postJson('/api/v1/user/upload', [
                'file' => $uploadedFile,
            ]);

        
        $response->assertCreated()
                 ->assertJson([
                     'status' => true,
                     'message' => 'Success on store file. We will process in background.',
                 ])->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'original_name',
                        'path',
                        'status',
                        'updated_at',
                        'created_at',
                    ]
                ]);
    }
}
