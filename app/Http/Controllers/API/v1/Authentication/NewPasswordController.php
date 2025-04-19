<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Services\v1\UsersServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewPasswordController extends Controller
{
    public function __construct(private UsersServices $usersServices){}

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $status = $this->usersServices->newPassword($request);
        return $this->sendSuccessResponse(['status' => __($status)], 'Password reset successfully');
    }
}
