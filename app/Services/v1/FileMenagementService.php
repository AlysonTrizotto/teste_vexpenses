<?php

namespace App\Services\v1;

use App\Enums\v1\FileManagementEnum;
use App\Http\Requests\v1\User\ImportUserRequest;
use App\Jobs\v1\ProcessFileJob;
use App\Models\v1\FileManagement;
use App\Models\v1\User;
use App\Services\v1\StorageManagerService;
use App\Traits\v1\CreateLog;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class FileMenagementService
{
    use CreateLog;

    public function __construct(
        private FileManagement $fileManagement,
        private StorageManagerService $storageManager
    ){}

    /**
     * Get the import progress for a given file management ID.
     *
     * @param int $fileManagementId The ID of the file management record.
     *
     * @return self The file management record.
     */
    public function getImportProgress(int $fileManagementId)
    {
        $this->createLog('Get import progress', 'Checking import progress for file management ID: ' . $fileManagementId);

        $percent = Cache::get("file_management_:{$fileManagementId}",0);
        if($percent !== null && $percent <= 100)
        {
            $fileManagement = Cache::remember("file_menagement_get_progress:{$fileManagementId}", now()->addMinutes(10), function()use ($fileManagementId){
                return collect($this->fileManagement->getById($fileManagementId));
            });
        }else{
            $fileManagement = collect($this->fileManagement->getById($fileManagementId));
        }

        $status = $fileManagement->get('status');

        $progress = match ($status) {
            FileManagementEnum::IN_PROGRESS->value => Cache::get("file_management_:{$fileManagementId}", 0),
            FileManagementEnum::SUCCESS_ON_PROCESS->value,
            FileManagementEnum::FAILED_ON_PROCESS->value => 100,
            FileManagementEnum::NOT_PROCESSED->value => 0,
            default => null
        };

        return $progress !== null
            ? $fileManagement->merge(['progress' => $progress])
            : $fileManagement;
    }

    
    public function importUsersFromFile(array $data)
    {
        $this->createLog('Import users from file', 'Importing a new file, creating a new record of file management');

        $fileName = Uuid::uuid4()->toString() . "." . $data["file"]->getClientOriginalExtension();
        $path = date('Y') . DIRECTORY_SEPARATOR . date('m') . DIRECTORY_SEPARATOR . date('d');
        
        $fileManagement = $this->fileManagement->store([
            'name' => $fileName,
            'original_name' => $data["file"]->getClientOriginalName(),
            'path' => $path . DIRECTORY_SEPARATOR . $fileName,
            'status' => FileManagementEnum::NOT_PROCESSED->value,
        ]);

        if(!$this->storageManager->putFile($path, $data["file"], $fileName)){
            $this->createLog('Error on store file', 'Error on store file, file not written');
            throw new Exception('Error on store file');
        }

        $this->createLog('File stored', 'File stored successfully, dispatching job to process file');
        dispatch(new ProcessFileJob($fileManagement));

        return $fileManagement;
    }


    /**
     * Processa o arquivo de usu rios.
     *
     * @param FileManagement $fileManagement
     *
     * @return void
     */
    public function processFile(FileManagement $fileManagement)
    {
        $fileManagement->status = FileManagementEnum::IN_PROGRESS->value;
        $fileManagement->save();

        $errors = [];
        $validBatch = [];
        $processed = 0;

        $totalLines = $this->countCsvLines($fileManagement->path);
        $cacheKey = "file_management_:{$fileManagement->id}";
    
        foreach ($this->lazyReadCsvWithValidation($fileManagement->path) as $line) {
            $processed++;
    
            // Atualiza cache da porcentagem
            if ($totalLines > 0) {
                $percentage = number_format(($processed / $totalLines) * 100, 2);
                Cache::put($cacheKey, $percentage, now()->addMinutes(120)); 
            }


            if ($line['valid']) {
                $validBatch[] = array_merge($line['data'], [
                    'password' => bcrypt('12345678'), 
                ]);

                // Inserção em lotes de 100
                if (count($validBatch) === 100) {
                    User::insert($validBatch);
                    $validBatch = [];
                }
            } else {
                $errors[] = $line['errors'];
                Log::warning('Erro na importação de linha', $line['errors']);
            }
        }

        // Inserção final, tudo que for menor que 100
        if (!empty($validBatch)) {
            User::insert($validBatch);
        }

        // Salva erros e status final
        if (!empty($errors)) {
            $fileManagement->error = json_encode(array_merge(
                json_decode($fileManagement->error, true) ?? [],
                ...$errors
            ));
            $fileManagement->status = FileManagementEnum::FAILED_ON_PROCESS->value;
        } else {
            $fileManagement->status = FileManagementEnum::SUCCESS_ON_PROCESS->value;
        }

        $fileManagement->save();

        Cache::put($cacheKey, 100.0, now()->addMinutes(120));
    }


    public function lazyReadCsvWithValidation(string $filePath): \Generator
    {
        $file = $this->storageManager->getPath($filePath);

        if (!$file || is_array($file)) {
            Log::error('Erro ao recuperar o caminho do arquivo');
            throw new Exception('Erro ao obter arquivo');
        }

        if (($handle = fopen($file, 'r')) !== false) {
            $header = null;

            while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                if (!$header) {
                    $header = $row;

                    // Validação do cabeçalho
                    $expected = ['Nome', 'E-mail', 'Data de nascimento'];
                    if ($header !== $expected) {
                        fclose($handle);
                        
                        Log::error('Cabeçalho CSV inválido. Esperado: ' . implode(', ', $expected));
                        throw new Exception('Cabeçalho CSV inválido. Esperado: ' . implode(', ', $expected));
                    }

                    continue;
                }

                $record = array_combine($header, $row);
                $validator = Validator::make($record, (new ImportUserRequest())->rules());

                if ($validator->fails()) {
                    yield [
                        'valid' => false,
                        'data' => $record,
                        'errors' => $validator->errors()->toArray(),
                    ];
                    continue;
                }

                yield [
                    'valid' => true,
                    'data' => [
                        'name' => $record['Nome'],
                        'email' => $record['E-mail'],
                        'birth_date' => $record['Data de nascimento'],
                    ],
                ];
            }

            fclose($handle);
        }
    }

    private function countCsvLines(string $filePath): int
    {
        $file = $this->storageManager->getPath($filePath);

        if (!$file || is_array($file)) {
            throw new Exception('Erro ao obter caminho do arquivo');
        }

        $lineCount = 0;
        if (($handle = fopen($file, 'r')) !== false) {
            while (fgets($handle) !== false) {
                $lineCount++;
            }
            fclose($handle);
        }

        return max(0, $lineCount - 1); // -1 para descontar o cabeçalho
    }


}