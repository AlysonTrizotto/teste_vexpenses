<?php

namespace App\Services\v1;

use App\Enums\v1\TokenAbility;
use App\Http\Requests\v1\Authentication\AtuthenticationRequest;
use App\Http\Requests\v1\Authentication\RegisterRequest;
use App\Jobs\v1\SendWelcomeMailJob;
use App\Models\v1\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UsersServices
{

    public function index(array $data)
    {
        return User::index($data);
    }

    public function create(RegisterRequest $request)
    {
        $user = User::store($request->validated());
        dispatch(new SendWelcomeMailJob($user->name, $user->email));
        return $user;
    }

    public function me(int $userId)
    {
        return User::me($userId);
    }


    public function authenticate(AtuthenticationRequest $request)
    {
        $request->authenticate();

        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado'], Response::HTTP_UNAUTHORIZED);
        }

       
        $user->tokens()->delete();

        
        $accessToken = $user->createToken('access_token', [TokenAbility::ACCESS_API->value, $user->user_type], 
            Carbon::now()->addMinutes(config('sanctum.ac_expiration'))
        );
        
        $refreshToken  = $user->createToken('refresh_token', [TokenAbility::ISSUE_ACCESS_TOKEN->value], 
            Carbon::now()->addMinutes(config('sanctum.rt_expiration'))
        )->plainTextToken;

        return [
                'token' => $accessToken->plainTextToken,
                'refresh_token' => $refreshToken,
                'time_to_expire_token' => $accessToken->accessToken->expires_at,
                'user' => $request->user()->only(['id', 'name', 'email']),
            ];
    }

    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken(); 
        $tokenId = explode("|", $token)[0]; 

        // Verifica se o token atual é um refresh token válido
        $refreshToken = $request->user()->tokens()
            ->where('id', '=', $tokenId)
            ->where('name', 'like', '%refresh_token%') 
            ->first();
            
        if (!$refreshToken) {
            return response()->json(['error' => 'Invalid or expired refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        // Verifica se o refresh token ainda está válido
        if (Carbon::parse($refreshToken->expires_at)->isPast()) {
            return response()->json(['error' => 'Refresh token expired'], Response::HTTP_UNAUTHORIZED);
        }

        
        $accessToken = $request->user()->createToken(
            'access_token',
            [TokenAbility::ACCESS_API->value], 
            Carbon::now()->addMinutes(config('sanctum.ac_expiration'))
        );

        return [
                'token' => $accessToken->plainTextToken,
                'time_to_expire_token' => $accessToken->accessToken->expires_at,
                'user' => $request->user()->only(['name', 'email']),
            ];
    }

}