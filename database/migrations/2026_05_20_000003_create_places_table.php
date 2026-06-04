<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('places')) {
            return;
        }

        Schema::create('places', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('short_description')->nullable();
            $table->text('description');
            $table->string('address');
            $table->string('city');
            $table->string('province')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('google_maps_url')->nullable();
            $table->string('status')->default('active');
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('bookmarks_count')->default(0);
            $table->unsignedInteger('reports_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category_id', 'status']);
            $table->index(['city', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('places');
    }
};
