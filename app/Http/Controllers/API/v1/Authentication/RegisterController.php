<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Enums\v1\HttpCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Authentication\RegisterRequest;
use App\Http\Resources\v1\UserResource;
use App\Services\v1\UsersServices;
use App\Traits\v1\CreateLog;

class RegisterController extends Controller
{
    public function __construct(private UsersServices $usersServices){}


    public function register(RegisterRequest $request)
    {
        try {
            return $this->sendSuccessResponse(new UserResource($this->usersServices->create($request)), 'Success on register user', HttpCodeEnum::CREATED->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Fail on register user');
        }
    }
}
