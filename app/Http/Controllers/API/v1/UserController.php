<?php

namespace App\Http\Controllers\API\v1;

use App\Enums\v1\HttpCodeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\User\UserIndexRequest;
use App\Http\Resources\v1\UserResource;
use App\Services\v1\UsersServices;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(private UsersServices $usersServices){}

    public function me()
    {
        try {
            return $this->sendSuccessResponse(new UserResource($this->usersServices->me(Auth::user()->id)), 'Success on get user', HttpCodeEnum::OK->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Fail on get user');
        }
    }

    public function index(UserIndexRequest $request)
    {
        try {
            return $this->sendSuccessResponse(new UserResource($this->usersServices->index($request->validated())), 'Success on get users', HttpCodeEnum::OK->value);
        } catch (\Exception $ex) {
            return $this->sendFailResponse($ex, 'Fail on get users');
        }
    }
}
