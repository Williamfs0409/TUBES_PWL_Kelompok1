<?php

namespace App\Http\Middleware;

use App\Support\CityZenAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CityZenAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to open admin panel.');
        }

        abort_unless(CityZenAccess::isAdmin($request->session()->get('cityzen_user')), 403);

        return $next($request);
    }
}
