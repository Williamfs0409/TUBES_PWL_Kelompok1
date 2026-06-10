<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CityZenAccess
{
    public static function roleNameForUserId(?int $userId): string
    {
        if (! $userId || ! Schema::hasTable('users') || ! Schema::hasTable('roles') || ! Schema::hasColumn('users', 'role_id')) {
            return 'user';
        }

        $roleName = DB::table('users')
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->where('users.id', $userId)
            ->value('roles.name');

        return strtolower((string) ($roleName ?: 'user'));
    }

    public static function sessionPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => self::roleNameForUserId((int) $user->id),
        ];
    }

    public static function isAdmin(?array $sessionUser): bool
    {
        return in_array(self::roleNameForUserId($sessionUser['id'] ?? null), ['admin', 'superadmin'], true);
    }

    public static function isSuperAdmin(?array $sessionUser): bool
    {
        return self::roleNameForUserId($sessionUser['id'] ?? null) === 'superadmin';
    }

    public static function profileStats(int $userId): array
    {
        return [
            'watched_places' => Schema::hasTable('bookmarks') ? DB::table('bookmarks')->where('user_id', $userId)->count() : 0,
            'reports_drafted' => Schema::hasTable('reports') ? DB::table('reports')->where('user_id', $userId)->count() : 0,
            'reviews_count' => Schema::hasTable('reviews') ? DB::table('reviews')->where('user_id', $userId)->count() : 0,
            'likes_count' => Schema::hasTable('likes') ? DB::table('likes')->where('user_id', $userId)->count() : 0,
        ];
    }
}
