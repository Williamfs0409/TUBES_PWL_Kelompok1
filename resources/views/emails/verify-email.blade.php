<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Verify your CityZen email</title>
</head>
<body style="margin:0;background:#f4f8f1;color:#17201a;font-family:Inter,Arial,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:560px;background:#fffef8;border:1px solid #174d2e;border-radius:18px;padding:32px;">
                    <tr>
                        <td>
                            <p style="color:#174d2e;font-size:12px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;margin:0 0 12px;">CityZen verification</p>
                            <h1 style="font-family:Arial,sans-serif;font-size:32px;line-height:1.05;margin:0 0 16px;">Verify your email, {{ $user->name }}.</h1>
                            <p style="color:#546459;font-size:16px;line-height:1.6;margin:0 0 24px;">Klik tombol di bawah untuk mengaktifkan akun CityZen kamu. Link ini berlaku selama {{ $expiresMinutes }} menit.</p>
                            <p style="margin:0 0 24px;">
                                <a href="{{ $verificationUrl }}" style="background:#0f5a2a;border:1px solid #0b331b;border-radius:999px;color:#ffffff;display:inline-block;font-weight:800;padding:13px 22px;text-decoration:none;">Verify Email</a>
                            </p>
                            <p style="color:#66736a;font-size:13px;line-height:1.6;margin:0;">Kalau tombol tidak bisa dibuka, salin link ini ke browser:</p>
                            <p style="color:#0f5a2a;font-size:13px;line-height:1.6;word-break:break-all;margin:8px 0 0;">{{ $verificationUrl }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
