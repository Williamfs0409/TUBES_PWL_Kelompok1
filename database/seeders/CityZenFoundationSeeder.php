<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CityZenFoundationSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $roles = $this->seedRoles();
            $this->seedSystemUsers($roles);
            $this->seedBadges();
            $this->seedCategories();
            $this->seedReportCategories();
            $this->seedReportStatuses();
            $this->seedNotificationTypes();
        });
    }

    private function seedRoles(): array
    {
        if (! Schema::hasTable('roles')) {
            return [];
        }

        $roles = [
            'user' => 'Pengguna umum CityZen.',
            'admin' => 'Moderator laporan dan konten CityZen.',
            'superadmin' => 'Pengelola penuh sistem CityZen.',
        ];

        foreach ($roles as $slug => $description) {
            $payload = [
                'name' => $slug,
                'slug' => $slug,
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('roles', 'description')) {
                $payload['description'] = $description;
            }

            if (Schema::hasColumn('roles', 'created_at')) {
                $payload['created_at'] = now();
            }

            DB::table('roles')->updateOrInsert(['slug' => $slug], $payload);
        }

        return DB::table('roles')->pluck('id', 'slug')->map(fn ($id) => (int) $id)->all();
    }

    private function seedSystemUsers(array $roles): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $accounts = [
            [
                'role' => 'admin',
                'name' => env('CITYZEN_ADMIN_NAME', 'CityZen Admin'),
                'email' => env('CITYZEN_ADMIN_EMAIL', 'admin@cityzen.test'),
                'password' => env('CITYZEN_ADMIN_PASSWORD', 'password'),
                'username' => 'admin',
            ],
            [
                'role' => 'superadmin',
                'name' => env('CITYZEN_SUPERADMIN_NAME', 'CityZen Superadmin'),
                'email' => env('CITYZEN_SUPERADMIN_EMAIL', 'superadmin@cityzen.test'),
                'password' => env('CITYZEN_SUPERADMIN_PASSWORD', 'password'),
                'username' => 'superadmin',
            ],
        ];

        foreach ($accounts as $account) {
            $payload = [
                'name' => $account['name'],
                'password' => Hash::make($account['password']),
                'email_verified_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('users', 'role_id') && isset($roles[$account['role']])) {
                $payload['role_id'] = $roles[$account['role']];
            }

            if (Schema::hasColumn('users', 'is_suspended')) {
                $payload['is_suspended'] = false;
                $payload['suspended_at'] = null;
            }

            $user = User::query()->updateOrCreate(
                ['email' => $account['email']],
                $payload
            );

            $this->ensureProfile($user, $account['username']);
        }
    }

    private function ensureProfile(User $user, string $baseUsername): void
    {
        if (! Schema::hasTable('profiles')) {
            return;
        }

        $payload = [
            'username' => $baseUsername,
            'avatar_path' => null,
            'city' => null,
            'bio' => null,
            'contribution_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('profiles', 'current_badge')) {
            $payload['current_badge'] = null;
        }

        DB::table('profiles')->updateOrInsert(['user_id' => $user->id], $payload);
    }

    private function seedBadges(): void
    {
        if (! Schema::hasTable('badges')) {
            return;
        }

        $badges = [
            ['Explorer', 'Mulai aktif menjelajah dan membagikan ruang publik.', '5 posting'],
            ['Contributor', 'Kontributor konsisten untuk data ruang publik.', '20 kontribusi'],
            ['Guardian', 'Aktif menjaga fasilitas publik melalui laporan valid.', '10 laporan valid'],
            ['City Hero', 'Kontributor unggulan komunitas CityZen.', 'Top contributor bulanan'],
        ];

        foreach ($badges as [$name, $description, $requirement]) {
            DB::table('badges')->updateOrInsert(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => $description,
                    'requirement_text' => $requirement,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function seedCategories(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        collect([
            'Taman Kota',
            'Wisata',
            'Kuliner',
            'Fasilitas Umum',
            'Tempat Olahraga',
            'Ruang Komunitas',
            'Edukasi',
            'Transportasi Publik',
            'Lainnya',
        ])->each(function ($name, $index) {
            DB::table('categories')->updateOrInsert(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => null,
                    'icon' => null,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        });
    }

    private function seedReportCategories(): void
    {
        if (! Schema::hasTable('report_categories')) {
            return;
        }

        collect([
            'Sampah',
            'Kerusakan fasilitas',
            'Keamanan',
            'Aksesibilitas',
            'Vandalisme',
            'Lainnya',
        ])->each(function ($name) {
            DB::table('report_categories')->updateOrInsert(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'description' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        });
    }

    private function seedReportStatuses(): void
    {
        if (! Schema::hasTable('report_statuses')) {
            return;
        }

        $statuses = [
            'pending' => 'Menunggu verifikasi',
            'verified' => 'Laporan valid',
            'rejected' => 'Laporan tidak valid',
            'resolved' => 'Masalah telah ditangani',
        ];

        foreach ($statuses as $slug => $description) {
            DB::table('report_statuses')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => Str::headline($slug),
                    'slug' => $slug,
                    'description' => $description,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function seedNotificationTypes(): void
    {
        if (! Schema::hasTable('notification_types')) {
            return;
        }

        $types = [
            'report' => 'Report moderation updates.',
            'review' => 'Review activity.',
            'badge' => 'Badge and gamification updates.',
            'system' => 'System announcements.',
        ];

        foreach ($types as $slug => $description) {
            DB::table('notification_types')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => Str::headline($slug),
                    'slug' => $slug,
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
