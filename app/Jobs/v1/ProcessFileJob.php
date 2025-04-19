<?php

namespace App\Jobs\v1;

use App\Enums\v1\FileManagementEnum;
use App\Models\v1\FileManagement;
use App\Services\v1\FileMenagementService;
use App\Traits\v1\CreateLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessFileJob implements ShouldQueue
{
    use Queueable, CreateLog;

    public $timeout = 0;

    public function __construct(
        public FileManagement $fileManagement
    ) {}

    public function handle(): void
    {
        try {
            $this->createLog('Process file', 'Processing file with ID: ' . $this->fileManagement->id);
            app(FileMenagementService::class)->processFile($this->fileManagement);
        } catch (\Exception $exception) {
            $this->createLog('Error on process file', 'Error on process file with ID: ' . $this->fileManagement->id);
            $this->fileManagement->status = FileManagementEnum::FAILED_ON_PROCESS->value;
            $this->fileManagement->error = $exception->getMessage();
            $this->fileManagement->save();
        }
    }
}
