<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('badges')) {
            Schema::table('badges', function (Blueprint $table) {
                if (! Schema::hasColumn('badges', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }

                if (! Schema::hasColumn('badges', 'requirement_text')) {
                    $table->string('requirement_text')->nullable()->after('description');
                }
            });

            DB::table('badges')->orderBy('id')->get()->each(function ($badge) {
                if (! ($badge->slug ?? null)) {
                    DB::table('badges')->where('id', $badge->id)->update([
                        'slug' => Str::slug($badge->name ?? 'badge-'.$badge->id),
                    ]);
                }
            });
        }

        if (Schema::hasTable('notification_types')) {
            Schema::table('notification_types', function (Blueprint $table) {
                if (! Schema::hasColumn('notification_types', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }

                if (! Schema::hasColumn('notification_types', 'description')) {
                    $table->text('description')->nullable()->after('slug');
                }
            });

            DB::table('notification_types')->orderBy('id')->get()->each(function ($type) {
                if (! ($type->slug ?? null)) {
                    DB::table('notification_types')->where('id', $type->id)->update([
                        'slug' => Str::slug($type->name ?? 'notification-type-'.$type->id),
                    ]);
                }
            });
        }
    }

    public function down(): void
    {
        //
    }
};
