<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CityZenAccess;
use App\Support\CityZenEmailVerification;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function notice(Request $request)
    {
        $user = User::findOrFail($request->session()->get('cityzen_user.id'));

        if ($user->email_verified_at) {
            return redirect('/dashboard');
        }

        return view('auth.verify-email', [
            'email' => $user->email,
        ]);
    }

    public function send(Request $request)
    {
        $user = User::findOrFail($request->session()->get('cityzen_user.id'));

        if ($user->email_verified_at) {
            return redirect('/dashboard');
        }

        $sent = CityZenEmailVerification::send($user);

        return back()->with(
            $sent ? 'status' : 'notice',
            $sent
                ? 'Link verifikasi baru sudah dikirim.'
                : 'Email belum bisa dikirim. Pastikan SMTP sudah diatur di environment.'
        );
    }

    public function verify(Request $request, int $id, string $hash)
    {
        $sessionUserId = (int) $request->session()->get('cityzen_user.id');

        abort_unless($sessionUserId === $id, 403);

        $user = User::findOrFail($id);

        abort_unless(hash_equals(sha1($user->email), $hash), 403);

        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        $request->session()->put('cityzen_user', CityZenAccess::sessionPayload($user));

        return redirect('/dashboard')->with('status', 'Email berhasil diverifikasi. Selamat datang di CityZen.');
    }
}
