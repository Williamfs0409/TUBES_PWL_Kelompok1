<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SettingController extends Controller
{
    public function edit(Request $request)
    {
        $userId = $request->session()->get('cityzen_user.id');
        $account = User::findOrFail($userId);
        $profile = Schema::hasTable('profiles')
            ? DB::table('profiles')->where('user_id', $userId)->first()
            : null;

        return view('settings', [
            'account' => $account,
            'profile' => $profile,
            'isAdmin' => CityZenAccess::isAdmin($request->session()->get('cityzen_user')),
        ]);
    }

    public function update(Request $request)
    {
        $userId = $request->session()->get('cityzen_user.id');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'unique:users,email,'.$userId],
            'username' => ['required', 'string', 'max:40', 'alpha_dash', 'unique:profiles,username,'.$userId.',user_id'],
            'city' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:4'],
        ]);

        $account = User::findOrFail($userId);
        $account->fill([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (! empty($data['password'])) {
            $account->password = Hash::make($data['password']);
        }

        $account->save();

        if (Schema::hasTable('profiles')) {
            DB::table('profiles')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'username' => $data['username'],
                    'city' => $data['city'] ?? null,
                    'bio' => $data['bio'] ?? null,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $request->session()->put('cityzen_user', CityZenAccess::sessionPayload($account));

        return redirect('/settings')->with('status', 'Settings berhasil diperbarui.');
    }
}
