<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\ForgotPasswordRequest;
// Removed the incorrect import as the trait is not found in the specified namespace


class ForgotPasswordController extends Controller
{
    // Removed the usage of the missing trait and implemented the functionality directly
    public function broker()
    {
        return Password::broker();
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \App\Http\Requests\Auth\ForgotPasswordRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
                    ? response()->json(['message' => 'Password reset link sent to your email.'])
                    : response()->json(['message' => 'Unable to send password reset link.'], 500);
    }
}
