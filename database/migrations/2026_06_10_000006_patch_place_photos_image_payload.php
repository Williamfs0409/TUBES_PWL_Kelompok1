<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('place_photos')) {
            return;
        }

        Schema::table('place_photos', function (Blueprint $table) {
            if (! Schema::hasColumn('place_photos', 'image_mime')) {
                $table->string('image_mime')->nullable()->after('image_path');
            }

            if (! Schema::hasColumn('place_photos', 'image_data')) {
                $table->longText('image_data')->nullable()->after('image_mime');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('place_photos')) {
            return;
        }

        Schema::table('place_photos', function (Blueprint $table) {
            if (Schema::hasColumn('place_photos', 'image_data')) {
                $table->dropColumn('image_data');
            }

            if (Schema::hasColumn('place_photos', 'image_mime')) {
                $table->dropColumn('image_mime');
            }
        });
    }
};
