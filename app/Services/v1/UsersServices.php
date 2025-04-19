<?php

namespace App\Services\v1;

use App\Http\Requests\v1\Authentication\AtuthenticationRequest;
use App\Http\Requests\v1\Authentication\RegisterRequest;
use App\Jobs\v1\SendWelcomeMailJob;
use App\Models\v1\User;
use App\Traits\v1\CreateLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\PasswordReset;

class UsersServices
{
    use CreateLog;

    public function __construct(private User $user){}

    public function index(array $data)
    {
        $this->createLog('User index', 'search: ' .  json_encode($data));
        return $this->user::index($data);
    }

    public function create(RegisterRequest $request)
    {
        $this->createLog('User create', 'data: ' .  json_encode($request->except(['password', 'password_confirmation'])));
        $user = $this->user::store($request->validated());
        dispatch(new SendWelcomeMailJob($user->name, $user->email));
        return $user;
    }

    public function me(int $userId)
    {
        $this->createLog('User me', 'userId: ' .  $userId);
        return $this->user::me($userId);
    }


    public function authenticate(AtuthenticationRequest $request)
    {
        $this->createLog('User authenticate', 'email: ' .  json_encode($request->only(['email'])));
        $credentials = $request->validated();

        if (!$token = JWTAuth::attempt($credentials)) {
            $this->createLog('User authenticate fail', 'invalid credentials for email: ' .  json_encode($request->only(['email'])));
            return [
                'error' => 'invalid_credentials',
            ];
        }

        return [
            'token' => $token,
            'expires_in' =>JWTAuth::factory()->getTTL() * 60,
            'user' => Auth::guard('api')->user()->only(['id', 'name', 'email']),
        ];
    }

    public function refreshToken(Request $request)
    {
        try {
            $this->createLog('User refresh token', 'email: ' .  json_encode($request->only(['email'])));
            $newToken = JWTAuth::parseToken()->refresh();
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            $this->createLog('User refresh token fail', 'token invalid: ' .  $e->getMessage());
            throw $e;
        }
    
        return [
            'token' => $newToken,
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60,
        ];
    }

    public function newPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $this->createLog('User reset password', 'data: ' .  json_encode($request->except(['password', 'password_confirmation'])));


        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->string('password')),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status != Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }

        $this->createLog('User reset password', 'Succefull on reset password: ' .  json_encode($request->except(['password', 'password_confirmation'])));
        return $status;
    }

    public function passwordResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $this->createLog('User reset password', 'email: ' .  $request->only(['email']));

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => [__($status)],
            ]);
        }


        $this->createLog('User reset password', 'Password reset link sent successfully');

        return $status;
    }

}