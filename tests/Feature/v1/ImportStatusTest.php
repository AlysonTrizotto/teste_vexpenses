<?php

namespace Tests\Feature\v1;

use App\Models\v1\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportStatusTest extends TestCase
{
    /**
     *
     * Verifica se:
     *  1. O upload de um arquivo CSV fake com usuários fake é realizado com sucesso;
     *  2. O status da importação é verificado após o upload;
     *  3. O status da importação é verificado após 20 segundos;
     *  4. O status da importação retorna o status correto e os dados da importação.
     *
     * @return void
     */
    public function test_it_uploads_dynamically_generated_csv_and_checks_import_status(): void
    {
        // 1. Cria usuários fake com factory
        $fakeUsers = User::factory()->count(3)->make([
            'birth_date' => now()->subYears(rand(18, 50))->format('Y-m-d'),
        ]);

        // 2. Monta dados CSV com cabeçalho e os usuários
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

        // 3. Cria arquivo CSV temporário e simula Storage
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

        // 4. Cria usuário autenticado e obtém token
        $user = $this->createAuthenticatedUser();
        $token = $this->authenticateAndGetToken($user);

        // 5. Executa a requisição de upload
        $uploadResponse = $this->withHeaders([
                'Authorization' => 'Bearer ' . $token,
            ])
            ->postJson('/api/v1/user/upload', [
                'file' => $uploadedFile,
            ]);

        $uploadResponse->assertCreated()
            ->assertJson([
                'status' => true,
                'message' => 'Success on store file. We will process in background.',
            ])
            ->assertJsonStructure([
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

        
        $importId = $uploadResponse->json('data.id');

        // 6. Aguarda job 
        sleep(20);

        // 7. Faz nova requisição para verificar o status da importação
        $statusResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson("/api/v1/user/import-status/{$importId}");

        // 8. Valida o retorno completo do status da importação
        $statusResponse->assertOk()
            ->assertJson([
                'status' => true,
                'message' => 'Success on store file. We will process in background.',
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'original_name',
                    'path',
                    'status',
                    'error',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'progress',
                ]
            ]);

        
        $this->assertIsInt($statusResponse->json('data.id'));
        $this->assertIsString($statusResponse->json('data.name'));
        $this->assertIsString($statusResponse->json('data.original_name'));
        $this->assertIsInt($statusResponse->json('data.status'));
        $this->assertIsString($statusResponse->json('data.created_at'));
        $this->assertIsInt($statusResponse->json('data.progress'));
    }

}
