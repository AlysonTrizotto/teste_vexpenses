<?php

namespace App\Traits\v1;

use App\Http\Controllers\API\v1\LogController;
use App\Http\Requests\v1\Log\LogStoreRequest;
use Illuminate\Support\Facades\Auth;

trait CreateLog
{
    public function createLog($action, $description)
    {
        $log = [
            'action' => $action,
            'description' => $description,
            'user_id' => Auth::user()?->id ?? null
        ];

        app(LogController::class)->store(new LogStoreRequest($log));
    }
}