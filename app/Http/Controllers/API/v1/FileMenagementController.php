<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\v1\HttpCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\User\ImportUsersFromFileRequest;
use App\Http\Resources\v1\FileMenagementResource;
use App\Services\v1\FileMenagementService;

class FileMenagementController extends Controller
{
    public function __construct(
        private FileMenagementService $fileMenagementService
    ){
        $this->fileMenagementService = $fileMenagementService;
    }

    public function importUsersFromFile(ImportUsersFromFileRequest $request)
    {
        try {
            return $this->sendSuccessResponse(
                new FileMenagementResource(
                    $this->fileMenagementService->importUsersFromFile($request->validated())
            ), 'Success on store file. We will process in background.', HttpCodeEnum::CREATED->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Fail on update user');
        }
    }

    public function getImportProgress(int $id)
    {
        try {
            return $this->sendSuccessResponse(
                new FileMenagementResource(
                    $this->fileMenagementService->getImportProgress($id)
            ), 'Success on store file. We will process in background.', HttpCodeEnum::CREATED->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Fail on update user');
        }
    }

}
