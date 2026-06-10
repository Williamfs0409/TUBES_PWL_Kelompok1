<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('profiles')) {
            return;
        }

        Schema::table('profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('profiles', 'avatar_mime')) {
                $table->string('avatar_mime')->nullable()->after('avatar_path');
            }

            if (! Schema::hasColumn('profiles', 'avatar_data')) {
                $table->longText('avatar_data')->nullable()->after('avatar_mime');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('profiles')) {
            return;
        }

        Schema::table('profiles', function (Blueprint $table) {
            if (Schema::hasColumn('profiles', 'avatar_data')) {
                $table->dropColumn('avatar_data');
            }

            if (Schema::hasColumn('profiles', 'avatar_mime')) {
                $table->dropColumn('avatar_mime');
            }
        });
    }
};
