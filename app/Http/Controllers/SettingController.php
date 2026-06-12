<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

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
            'avatar' => ['nullable', 'image', 'max:2048'],
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
            $existingAvatar = DB::table('profiles')->where('user_id', $userId)->value('avatar_path');
            $avatarPath = $existingAvatar;
            $avatarMime = Schema::hasColumn('profiles', 'avatar_mime')
                ? DB::table('profiles')->where('user_id', $userId)->value('avatar_mime')
                : null;
            $avatarData = Schema::hasColumn('profiles', 'avatar_data')
                ? DB::table('profiles')->where('user_id', $userId)->value('avatar_data')
                : null;

            if ($request->hasFile('avatar')) {
                if ($existingAvatar && str_starts_with($existingAvatar, 'storage/')) {
                    Storage::disk('public')->delete(str($existingAvatar)->after('storage/')->toString());
                }

                $avatar = $request->file('avatar');
                $avatarPath = 'storage/'.$avatar->store('avatars', 'public');
                $avatarMime = $avatar->getMimeType();
                $avatarData = base64_encode(file_get_contents($avatar->getRealPath()));
            }

            $profilePayload = [
                'username' => $data['username'],
                'avatar_path' => $avatarPath,
                'city' => $data['city'] ?? null,
                'bio' => $data['bio'] ?? null,
                'updated_at' => now(),
                'created_at' => now(),
            ];

            if (Schema::hasColumn('profiles', 'avatar_mime')) {
                $profilePayload['avatar_mime'] = $avatarMime;
            }

            if (Schema::hasColumn('profiles', 'avatar_data')) {
                $profilePayload['avatar_data'] = $avatarData;
            }

            DB::table('profiles')->updateOrInsert(
                ['user_id' => $userId],
                $profilePayload
            );
        }

        $request->session()->put('cityzen_user', CityZenAccess::sessionPayload($account));

        return redirect('/settings')->with('status', 'Settings berhasil diperbarui.');
    }

    public function avatar(User $user)
    {
        abort_unless(Schema::hasTable('profiles'), 404);

        $profile = DB::table('profiles')->where('user_id', $user->id)->first();
        abort_unless($profile, 404);

        if (
            Schema::hasColumn('profiles', 'avatar_data')
            && Schema::hasColumn('profiles', 'avatar_mime')
            && ! empty($profile->avatar_data)
            && ! empty($profile->avatar_mime)
        ) {
            return response(base64_decode($profile->avatar_data), 200, [
                'Content-Type' => $profile->avatar_mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        if (! empty($profile->avatar_path) && str_starts_with($profile->avatar_path, 'storage/')) {
            $relativePath = str($profile->avatar_path)->after('storage/')->toString();

            if (Storage::disk('public')->exists($relativePath)) {
                return response()->file(Storage::disk('public')->path($relativePath), [
                    'Cache-Control' => 'public, max-age=3600',
                ]);
            }
        }

        abort(404);
    }
}
