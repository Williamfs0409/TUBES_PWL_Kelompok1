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
        if (! Schema::hasTable('report_statuses')) {
            return;
        }

        Schema::table('report_statuses', function (Blueprint $table) {
            if (! Schema::hasColumn('report_statuses', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }
        });

        DB::table('report_statuses')
            ->orderBy('id')
            ->get()
            ->each(function ($status) {
                $slug = $status->slug ?? null;

                if (! $slug) {
                    DB::table('report_statuses')
                        ->where('id', $status->id)
                        ->update(['slug' => Str::slug($status->name ?? 'status-'.$status->id)]);
                }
            });
    }

    public function down(): void
    {
        if (Schema::hasTable('report_statuses') && Schema::hasColumn('report_statuses', 'slug')) {
            Schema::table('report_statuses', function (Blueprint $table) {
                $table->dropColumn('slug');
            });
        }
    }
};
