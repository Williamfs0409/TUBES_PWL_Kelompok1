<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CityZenAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to continue.');
        }

        $userId = $request->session()->get('cityzen_user.id');
        $user = $userId ? User::find($userId) : null;

        if (! $user) {
            $request->session()->forget('cityzen_user');

            return redirect('/login')->with('notice', 'Please login to continue.');
        }

        if (Schema::hasColumn('users', 'is_suspended') && $user->is_suspended) {
            $request->session()->forget('cityzen_user');

            return redirect('/login')->withErrors(['email' => 'Akun CityZen ini sedang disuspend oleh admin.']);
        }

        return $next($request);
    }
}
