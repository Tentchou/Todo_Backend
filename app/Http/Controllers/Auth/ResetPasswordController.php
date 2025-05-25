<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use App\Http\Requests\Auth\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    // Removed ResetsPasswords trait as it is no longer available

    /**
     * Reset the given user's password.
     *
     * @param  \App\Http\Requests\Auth\ResetPasswordRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reset(ResetPasswordRequest $request)
    {
        $response = app('auth.password.broker')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $this->guard()->login($user); // Connecte l'utilisateur après la réinitialisation
                event(new PasswordReset($user));
            }
        );

        return $response == Password::PASSWORD_RESET
                    ? response()->json(['message' => 'Your password has been reset.'])
                    : response()->json(['message' => 'Unable to reset password.'], 500);
    }
}
