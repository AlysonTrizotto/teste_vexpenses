<?php

namespace App\Jobs\v1;

use App\Models\v1\FileManagement;
use App\Services\v1\FileMenagementService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessFileJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 0;

    public function __construct(
        public FileManagement $fileManagement
    ) {}

    public function handle(): void
    {
        app(FileMenagementService::class)->processFile($this->fileManagement);
    }
}
