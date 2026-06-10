<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Throwable;

class CityZenEmailVerification
{
    public static function send(User $user): bool
    {
        if ($user->email_verified_at) {
            return true;
        }

        $expiresMinutes = 60;
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes($expiresMinutes),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        try {
            Mail::send('emails.verify-email', [
                'user' => $user,
                'verificationUrl' => $verificationUrl,
                'expiresMinutes' => $expiresMinutes,
            ], function ($message) use ($user) {
                $message
                    ->to($user->email, $user->name)
                    ->subject('Verify your CityZen email');
            });

            return true;
        } catch (Throwable $exception) {
            Log::warning('CityZen email verification failed to send.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }
}
