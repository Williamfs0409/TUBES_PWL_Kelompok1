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
        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('name');
            }

            if (! Schema::hasColumn('roles', 'created_at')) {
                $table->timestamps();
            }
        });

        collect(['user', 'admin', 'superadmin'])->each(function ($name) {
            $payload = [
                'name' => $name,
            ];

            if (Schema::hasColumn('roles', 'updated_at')) {
                $payload['updated_at'] = now();
            }

            if (Schema::hasColumn('roles', 'created_at')) {
                $payload['created_at'] = now();
            }

            $existingId = DB::table('roles')->where('name', $name)->value('id');

            if ($existingId) {
                DB::table('roles')->where('id', $existingId)->update(array_merge($payload, ['slug' => $name]));
            } else {
                DB::table('roles')->insert(array_merge($payload, ['slug' => $name]));
            }
        });

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (! Schema::hasColumn('users', 'role_id')) {
                    $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
                }

                if (! Schema::hasColumn('users', 'is_suspended')) {
                    $table->boolean('is_suspended')->default(false)->after('role_id');
                }

                if (! Schema::hasColumn('users', 'suspended_at')) {
                    $table->timestamp('suspended_at')->nullable()->after('is_suspended');
                }
            });

            $userRoleId = DB::table('roles')->where('slug', 'user')->value('id');

            if ($userRoleId && Schema::hasColumn('users', 'role_id')) {
                DB::table('users')->whereNull('role_id')->update(['role_id' => $userRoleId]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'suspended_at')) {
                    $table->dropColumn('suspended_at');
                }

                if (Schema::hasColumn('users', 'is_suspended')) {
                    $table->dropColumn('is_suspended');
                }

                if (Schema::hasColumn('users', 'role_id')) {
                    $table->dropConstrainedForeignId('role_id');
                }
            });
        }

        Schema::dropIfExists('roles');
    }
};
