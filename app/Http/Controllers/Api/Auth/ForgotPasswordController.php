<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function send(ForgotPasswordRequest $request): JsonResponse
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return response()->json([
            'status' => $status,
            'message' => 'If an account exists with this email, a password reset link has been sent.',
        ]);
    }

    public function reset(ResetPasswordRequest $request, string $token): JsonResponse {
        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function($user, $password) {
            $user->forceFill([
                'password' => $password
            ])->save();

            event(new PasswordReset($user));
        });

        if ($status !== Password::PASSWORD_RESET) {
            return response()->json([
                'message' => __($status),
            ], 400);
        }

        return response()->json([
            'message' => 'Password reset successfully.',
        ]);
    }
}
