<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\v1\HttpCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Log\LogIndexRequest;
use App\Http\Requests\v1\Log\LogStoreRequest;
use App\Http\Resources\v1\LogResource;
use App\Models\v1\Log;
use App\Services\v1\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log as FacadesLog;
use Illuminate\Support\Facades\Validator;

class LogController extends Controller
{
    public function __construct(private LogService $logService)
    {
        $this->logService = new LogService();
    }

    public function index(LogIndexRequest $request)
    {
        try {
            $logs = $this->logService->index($request->validated());
            return $this->sendSuccessResponse(new LogResource($logs), 'Logs recuperados com sucesso', HttpCodeEnum::OK->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Erro ao buscar os logs');
        }
    }

    public function store(LogStoreRequest $request)
    {
        try {
            $validation = Validator::make($request->all(), $request->rules());

            if ($validation->fails()) {
                FacadesLog::error('Error on create log database.', $validation->errors()->all());
            }

            $log = Log::create($request->all());
            return $this->sendSuccessResponse(new LogResource($log), 'Log criado com sucesso', HttpCodeEnum::CREATED->value);
        } catch (\Exception $ex) {
            FacadesLog::error('Error on create log database.', $ex->getMessage());
        }
    }

    public function show(int $id)
    {
        try {
            
            $log = $this->logService->getById($id);
            return $this->sendSuccessResponse(new LogResource($log), 'Log recuperado com sucesso', HttpCodeEnum::OK->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Erro ao buscar o log');
        }
    }
}
