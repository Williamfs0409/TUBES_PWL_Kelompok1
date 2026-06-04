<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (! Schema::hasColumn('reports', 'report_category_id')) {
                $table->foreignId('report_category_id')
                    ->nullable()
                    ->after('place_id')
                    ->constrained('report_categories')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('reports', 'report_status_id')) {
                $table->foreignId('report_status_id')
                    ->nullable()
                    ->after('report_category_id')
                    ->constrained('report_statuses')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            if (Schema::hasColumn('reports', 'report_status_id')) {
                $table->dropConstrainedForeignId('report_status_id');
            }

            if (Schema::hasColumn('reports', 'report_category_id')) {
                $table->dropConstrainedForeignId('report_category_id');
            }
        });
    }
};
