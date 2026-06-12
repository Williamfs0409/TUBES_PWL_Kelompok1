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
        $statusFilter = in_array($request->query('status'), ['active', 'suspended'], true)
            ? $request->query('status')
            : 'all';
        $roleFilter = $request->query('role', 'all');
        $roles = Schema::hasTable('roles') ? DB::table('roles')->orderBy('id')->get() : collect();

        if ($roleFilter !== 'all' && ! $roles->contains('slug', $roleFilter)) {
            $roleFilter = 'all';
        }

        $users = User::query()
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->select('users.*', 'roles.name as role_name', 'profiles.avatar_path as avatar_path')
            ->when($statusFilter === 'active', fn ($query) => $query->where(function ($query) {
                $query->whereNull('users.is_suspended')->orWhere('users.is_suspended', false);
            }))
            ->when($statusFilter === 'suspended', fn ($query) => $query->where('users.is_suspended', true))
            ->when($roleFilter !== 'all', fn ($query) => $query->where('roles.slug', $roleFilter))
            ->orderBy('users.name')
            ->get();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $roles,
            'statusFilter' => $statusFilter,
            'roleFilter' => $roleFilter,
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
