<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->select('users.*', 'roles.name as role_name')
            ->orderBy('users.name')
            ->get();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Schema::hasTable('roles') ? DB::table('roles')->orderBy('id')->get() : collect(),
            'isSuperAdmin' => CityZenAccess::isSuperAdmin($request->session()->get('cityzen_user')),
            'currentUserId' => $request->session()->get('cityzen_user.id'),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'is_suspended' => ['nullable', 'boolean'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);

        $currentUserId = $request->session()->get('cityzen_user.id');

        if ((int) $currentUserId !== (int) $user->id && Schema::hasColumn('users', 'is_suspended')) {
            $shouldSuspend = (bool) ($data['is_suspended'] ?? false);
            $user->is_suspended = $shouldSuspend;
            $user->suspended_at = $shouldSuspend ? now() : null;
        }

        if (CityZenAccess::isSuperAdmin($request->session()->get('cityzen_user')) && Schema::hasColumn('users', 'role_id') && isset($data['role_id'])) {
            $user->role_id = $data['role_id'];
        }

        $user->save();

        return back()->with('status', 'User berhasil diperbarui.');
    }
}
