<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Enums\v1\HttpCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Authentication\AtuthenticationRequest;
use App\Http\Resources\v1\UserResource;
use App\Services\v1\UsersServices;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    public function __construct(
        private UsersServices $usersServices
    ){
        $this->usersServices = $usersServices;
    }


    public function authenticate(AtuthenticationRequest $request)
    {
        try {
            return $this->sendSuccessResponse(new UserResource($this->usersServices->authenticate($request)), 'Success on login user', HttpCodeEnum::CREATED->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Fail on login user');
        }
    }

    public function refreshToken(Request $request)
    {
        try {
            return $this->sendSuccessResponse(new UserResource($this->usersServices->refreshToken($request)), 'Success on refresh user', HttpCodeEnum::CREATED->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Fail on refresh user');
        }
    }

    public function destroy(Request $request)
    {
        try {
            return $this->sendSuccessResponse(new UserResource($this->usersServices->delete($request)), 'Success on logout user', HttpCodeEnum::CREATED->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Fail on logout user');
        }
    }
}
