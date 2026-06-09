<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('roles') || ! Schema::hasColumn('users', 'role_id')) {
            return;
        }

        $adminRoleIds = DB::table('roles')
            ->whereIn('slug', ['admin', 'superadmin'])
            ->pluck('id');

        $hasAdmin = DB::table('users')->whereIn('role_id', $adminRoleIds)->exists();

        if ($hasAdmin) {
            return;
        }

        $superadminRoleId = DB::table('roles')->where('slug', 'superadmin')->value('id');
        $firstUserId = DB::table('users')->orderBy('id')->value('id');

        if ($superadminRoleId && $firstUserId) {
            DB::table('users')->where('id', $firstUserId)->update(['role_id' => $superadminRoleId]);
        }
    }

    public function down(): void
    {
        //
    }
};
