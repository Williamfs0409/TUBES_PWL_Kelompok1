<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('cityzen:test-mail {to=debug@cityzen.test}', function (string $to) {
    $this->line('mailer='.config('mail.default'));
    $this->line('scheme='.(config('mail.mailers.smtp.scheme') ?? 'empty'));
    $this->line('host='.config('mail.mailers.smtp.host'));
    $this->line('port='.config('mail.mailers.smtp.port'));
    $this->line('username_set='.(filled(config('mail.mailers.smtp.username')) ? 'yes' : 'no'));
    $this->line('password_set='.(filled(config('mail.mailers.smtp.password')) ? 'yes' : 'no'));
    $this->line('from='.config('mail.from.address'));

    try {
        Mail::raw('CityZen SMTP diagnostic email.', function ($message) use ($to) {
            $message->to($to)->subject('CityZen SMTP diagnostic');
        });

        $this->info('send=ok');

        return self::SUCCESS;
    } catch (Throwable $exception) {
        $this->error('send=fail');
        $this->error($exception::class);
        $this->error($exception->getMessage());

        return self::FAILURE;
    }
})->purpose('Send a CityZen SMTP diagnostic email.');

Artisan::command('cityzen:test-verification-mail {email=debug@cityzen.test}', function (string $email) {
    $user = User::firstOrCreate(
        ['email' => $email],
        ['name' => 'Mail Diagnostic', 'password' => bcrypt('password')]
    );

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    try {
        Mail::send('emails.verify-email', [
            'user' => $user,
            'verificationUrl' => $verificationUrl,
            'expiresMinutes' => 60,
        ], function ($message) use ($user) {
            $message
                ->to($user->email, $user->name)
                ->subject('Verify your CityZen email');
        });

        $this->info('send=ok');

        return self::SUCCESS;
    } catch (Throwable $exception) {
        $this->error('send=fail');
        $this->error($exception::class);
        $this->error($exception->getMessage());

        return self::FAILURE;
    }
})->purpose('Send a CityZen verification email diagnostic.');
