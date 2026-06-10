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
        if (Schema::hasTable('report_categories')) {
            Schema::table('report_categories', function (Blueprint $table) {
                if (! Schema::hasColumn('report_categories', 'slug')) {
                    $table->string('slug')->nullable()->after('name');
                }

                if (! Schema::hasColumn('report_categories', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('description');
                }
            });

            DB::table('report_categories')
                ->orderBy('id')
                ->get()
                ->each(function ($category) {
                    if (! ($category->slug ?? null)) {
                        DB::table('report_categories')
                            ->where('id', $category->id)
                            ->update(['slug' => Str::slug($category->name ?? 'report-category-'.$category->id)]);
                    }
                });
        }

        if (Schema::hasTable('report_statuses')) {
            Schema::table('report_statuses', function (Blueprint $table) {
                if (! Schema::hasColumn('report_statuses', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('description');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('report_statuses') && Schema::hasColumn('report_statuses', 'is_active')) {
            Schema::table('report_statuses', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasTable('report_categories')) {
            Schema::table('report_categories', function (Blueprint $table) {
                if (Schema::hasColumn('report_categories', 'is_active')) {
                    $table->dropColumn('is_active');
                }

                if (Schema::hasColumn('report_categories', 'slug')) {
                    $table->dropColumn('slug');
                }
            });
        }
    }
};
