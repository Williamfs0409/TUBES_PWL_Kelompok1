<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class CityZenEnsureEmailIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasColumn('users', 'email_verified_at')) {
            return $next($request);
        }

        $userId = $request->session()->get('cityzen_user.id');
        $user = $userId ? User::find($userId) : null;

        if ($user && ! $user->email_verified_at) {
            return redirect()
                ->route('verification.notice')
                ->with('notice', 'Verifikasi email dulu untuk membuka fitur CityZen.');
        }

        return $next($request);
    }
}
