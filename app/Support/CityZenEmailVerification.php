<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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
            if (filled(config('services.mailtrap.api_token')) && filled(config('services.mailtrap.inbox_id'))) {
                return self::sendViaMailtrapApi($user, $verificationUrl, $expiresMinutes);
            }

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
            if (app()->bound('session')) {
                session()->flash('mail_error', $exception::class.': '.$exception->getMessage());
            }

            Log::warning('CityZen email verification failed to send.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private static function sendViaMailtrapApi(User $user, string $verificationUrl, int $expiresMinutes): bool
    {
        $html = View::make('emails.verify-email', [
            'user' => $user,
            'verificationUrl' => $verificationUrl,
            'expiresMinutes' => $expiresMinutes,
        ])->render();

        $endpoint = rtrim((string) config('services.mailtrap.endpoint'), '/');

        if (! $endpoint) {
            $endpoint = 'https://sandbox.api.mailtrap.io/api/send/'.config('services.mailtrap.inbox_id');
        }

        $response = Http::withToken(config('services.mailtrap.api_token'))
            ->acceptJson()
            ->asJson()
            ->timeout(20)
            ->post($endpoint, [
                'from' => [
                    'email' => config('mail.from.address') ?: 'noreply@cityzen.test',
                    'name' => config('mail.from.name') ?: 'CityZen',
                ],
                'to' => [[
                    'email' => $user->email,
                    'name' => $user->name,
                ]],
                'subject' => 'Verify your CityZen email',
                'text' => 'Verify your CityZen email: '.$verificationUrl,
                'html' => $html,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Mailtrap API failed with HTTP '.$response->status().': '.$response->body());
        }

        return true;
    }
}
