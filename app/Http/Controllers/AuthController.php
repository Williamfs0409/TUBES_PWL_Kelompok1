<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->has('cityzen_user')) {
            return redirect('/dashboard');
        }

        return view('auth.cityzen', ['mode' => 'login']);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:4'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'Email atau password CityZen belum sesuai.'])
                ->onlyInput('email');
        }

        if (Schema::hasColumn('users', 'is_suspended') && $user->is_suspended) {
            return back()
                ->with('suspended_warning', 'Akses Anda telah dibatasi oleh administrator. Silakan hubungi dukungan jika Anda merasa ini adalah kesalahan.')
                ->onlyInput('email');
        }

        $request->session()->put('cityzen_user', CityZenAccess::sessionPayload($user));
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function showRegister(Request $request)
    {
        if ($request->session()->has('cityzen_user')) {
            return redirect('/dashboard');
        }

        return view('auth.cityzen', ['mode' => 'register']);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => [
                'required',
                'string',
                'max:40',
                'alpha_dash',
                Rule::unique('profiles', 'username'),
            ],
            'password' => ['required', 'min:4'],
        ]);

        $profileUsername = $data['username'];
        unset($data['username']);

        if (Schema::hasTable('roles') && Schema::hasColumn('users', 'role_id')) {
            $data['role_id'] = DB::table('roles')->where('slug', 'user')->value('id');
        }

        if (Schema::hasColumn('users', 'email_verified_at')) {
            $data['email_verified_at'] = now();
        }

        $user = User::create($data);

        if (Schema::hasTable('profiles')) {
            DB::table('profiles')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'username' => $profileUsername,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $request->session()->put('cityzen_user', CityZenAccess::sessionPayload($user));
        $request->session()->regenerate();

        return redirect('/dashboard')->with('status', 'Akun CityZen berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('cityzen_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'You have been logged out.');
    }

}
