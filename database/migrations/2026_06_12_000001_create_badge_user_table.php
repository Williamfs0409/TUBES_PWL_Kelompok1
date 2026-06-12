<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('badge_user')) {
            Schema::create('badge_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
                $table->string('source_table')->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->json('progress_snapshot')->nullable();
                $table->timestamp('earned_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'badge_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('badge_user');
    }
};
