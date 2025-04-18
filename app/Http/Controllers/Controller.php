<?php

namespace App\Http\Controllers;

use App\Enums\v1\HttpCodeEnum;
use Exception;

abstract class Controller
{
    public function sendSuccessResponse(mixed  $data, string $message = 'Success', int $status = HttpCodeEnum::OK->value)
    {
        return response()->json(['status' => true, 'message' => $message, 'data' => $data], $status);
    }

    public function sendFailResponse(Exception $error, string $message = 'Fail', int $status = HttpCodeEnum::BAD_REQUEST->value)
    {
        return response()->json(['status' => false, 'message' => $message, 'errors' => $error->getMessage()], $status);
    }
}
