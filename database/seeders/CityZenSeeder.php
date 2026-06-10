<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CityZenSeeder extends Seeder
{
    /**
     * Seed demo rows for local presentation and feature tests.
     *
     * Production deployment uses CityZenFoundationSeeder through DatabaseSeeder.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $badges = $this->seedBadges();
            $roles = $this->seedRoles();
            $users = $this->seedUsers($badges, $roles);
            $categories = $this->seedCategories();
            $places = $this->seedPlaces($users, $categories);

            $this->seedPlacePhotos($places, $users);
            $this->seedReviews($places, $users);
            $this->seedLikes($places, $users);
            $this->seedBookmarks($places, $users);
            $reportTaxonomy = $this->seedReportTaxonomy();
            $this->seedReports($places, $users, $reportTaxonomy['categories'], $reportTaxonomy['statuses']);
            $this->refreshPlaceCounters();
        });
    }

    /**
     * @return array<string, int>
     */
    private function seedUsers(array $badges = [], array $roles = []): array
    {
        $now = now();
        $dummyUsers = [
            [
                'name' => 'Naufal City Explorer',
                'email' => 'naufal@cityzen.test',
                'username' => 'naufal_explorer',
                'city' => 'Jakarta',
                'bio' => 'Suka eksplor taman kota dan ruang publik ramah pejalan kaki.',
                'current_badge' => 'Explorer',
                'contribution_count' => 8,
            ],
            [
                'name' => 'Alya Urban Guardian',
                'email' => 'alya@cityzen.test',
                'username' => 'alya_guardian',
                'city' => 'Bandung',
                'bio' => 'Aktif melaporkan fasilitas publik yang perlu diperbaiki.',
                'current_badge' => 'Guardian',
                'contribution_count' => 14,
            ],
            [
                'name' => 'Raka Community Mapper',
                'email' => 'raka@cityzen.test',
                'username' => 'raka_mapper',
                'city' => 'Yogyakarta',
                'bio' => 'Mendokumentasikan ruang komunitas dan area publik inklusif.',
                'current_badge' => 'Contributor',
                'contribution_count' => 21,
            ],
            [
                'name' => 'Mira Transit Scout',
                'email' => 'mira@cityzen.test',
                'username' => 'mira_transit',
                'city' => 'Surabaya',
                'bio' => 'Memantau akses transportasi publik dan jalur pedestrian.',
                'current_badge' => 'Explorer',
                'contribution_count' => 11,
            ],
            [
                'name' => 'Dimas Green Reviewer',
                'email' => 'dimas@cityzen.test',
                'username' => 'dimas_green',
                'city' => 'Medan',
                'bio' => 'Menulis review ruang hijau dan fasilitas olahraga publik.',
                'current_badge' => 'Contributor',
                'contribution_count' => 18,
            ],
            [
                'name' => 'CityZen Admin',
                'email' => 'admin@cityzen.test',
                'username' => 'cityzen_admin',
                'city' => 'Jakarta',
                'bio' => 'Akun dummy untuk simulasi verifikasi laporan.',
                'current_badge' => 'City Hero',
                'contribution_count' => 32,
            ],
        ];

        $users = [];

        foreach ($dummyUsers as $dummyUser) {
            $roleSlug = $dummyUser['email'] === 'admin@cityzen.test' ? 'admin' : 'user';
            $userPayload = [
                'name' => $dummyUser['name'],
                'password' => Hash::make('password'),
                'email_verified_at' => $now,
            ];

            if (Schema::hasColumn('users', 'role_id') && isset($roles[$roleSlug])) {
                $userPayload['role_id'] = $roles[$roleSlug];
            }

            if (Schema::hasColumn('users', 'is_suspended')) {
                $userPayload['is_suspended'] = false;
            }

            if (Schema::hasColumn('users', 'status')) {
                $userPayload['status'] = 'active';
            }

            $user = User::query()->updateOrCreate(
                ['email' => $dummyUser['email']],
                $userPayload
            );

            $profilePayload = [
                'username' => $dummyUser['username'],
                'avatar_path' => null,
                'city' => $dummyUser['city'],
                'bio' => $dummyUser['bio'],
                'contribution_count' => $dummyUser['contribution_count'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('profiles', 'current_badge')) {
                $profilePayload['current_badge'] = $dummyUser['current_badge'];
            }

            if (Schema::hasColumn('profiles', 'current_badge_id')) {
                $profilePayload['current_badge_id'] = $badges[Str::slug($dummyUser['current_badge'])] ?? null;
            }

            DB::table('profiles')->updateOrInsert(
                ['user_id' => $user->id],
                $profilePayload
            );

            $users[$dummyUser['email']] = $user->id;
        }

        return $users;
    }

    /**
     * @return array<string, int>
     */
    private function seedRoles(): array
    {
        if (! Schema::hasTable('roles')) {
            return [];
        }

        $now = now();
        $roles = [
            ['name' => 'user', 'description' => 'Pengguna umum CityZen.'],
            ['name' => 'admin', 'description' => 'Moderator laporan dan konten CityZen.'],
            ['name' => 'superadmin', 'description' => 'Pengelola penuh sistem CityZen.'],
        ];
        $roleIds = [];

        foreach ($roles as $role) {
            $slug = Str::slug($role['name']);
            $payload = [
                'name' => $role['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('roles', 'slug')) {
                $payload['slug'] = $slug;
            }

            if (Schema::hasColumn('roles', 'description')) {
                $payload['description'] = $role['description'];
            }

            DB::table('roles')->updateOrInsert(
                Schema::hasColumn('roles', 'slug') ? ['slug' => $slug] : ['name' => $role['name']],
                $payload
            );

            $roleIds[$slug] = (int) DB::table('roles')
                ->where(Schema::hasColumn('roles', 'slug') ? 'slug' : 'name', Schema::hasColumn('roles', 'slug') ? $slug : $role['name'])
                ->value('id');
        }

        return $roleIds;
    }

    /**
     * @return array<string, int>
     */
    private function seedBadges(): array
    {
        if (! Schema::hasTable('badges')) {
            return [];
        }

        $now = now();
        $badges = [
            ['name' => 'Explorer', 'description' => 'Mulai aktif menjelajah dan membagikan ruang publik.', 'requirement_text' => '5 posting'],
            ['name' => 'Contributor', 'description' => 'Kontributor konsisten untuk data ruang publik.', 'requirement_text' => '20 kontribusi'],
            ['name' => 'Guardian', 'description' => 'Aktif menjaga kualitas fasilitas publik melalui laporan valid.', 'requirement_text' => '10 laporan valid'],
            ['name' => 'City Hero', 'description' => 'Kontributor unggulan komunitas CityZen.', 'requirement_text' => 'Top contributor bulanan'],
        ];
        $badgeIds = [];

        foreach ($badges as $badge) {
            $slug = Str::slug($badge['name']);
            $payload = [
                'name' => $badge['name'],
                'slug' => $slug,
                'description' => $badge['description'],
                'requirement_text' => $badge['requirement_text'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            DB::table('badges')->updateOrInsert(['slug' => $slug], $payload);
            $badgeIds[$slug] = (int) DB::table('badges')->where('slug', $slug)->value('id');
        }

        return $badgeIds;
    }

    /**
     * @return array<string, int>
     */
    private function seedCategories(): array
    {
        $now = now();
        $categories = [
            ['name' => 'Taman Kota', 'description' => 'Ruang hijau terbuka untuk rekreasi warga.', 'icon' => 'tree-pine'],
            ['name' => 'Wisata', 'description' => 'Destinasi publik untuk kunjungan lokal.', 'icon' => 'map-pin'],
            ['name' => 'Kuliner', 'description' => 'Area makan publik dan sentra UMKM.', 'icon' => 'utensils'],
            ['name' => 'Fasilitas Umum', 'description' => 'Fasilitas kota yang digunakan masyarakat luas.', 'icon' => 'building-2'],
            ['name' => 'Tempat Olahraga', 'description' => 'Lapangan dan area olahraga publik.', 'icon' => 'dumbbell'],
            ['name' => 'Ruang Komunitas', 'description' => 'Ruang berkumpul, edukasi, dan aktivitas komunitas.', 'icon' => 'users'],
        ];

        $categoryIds = [];

        foreach ($categories as $index => $category) {
            $slug = Str::slug($category['name']);

            DB::table('categories')->updateOrInsert(
                ['slug' => $slug],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'icon' => $category['icon'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $categoryIds[$slug] = (int) DB::table('categories')->where('slug', $slug)->value('id');
        }

        return $categoryIds;
    }

    /**
     * @param array<string, int> $users
     * @param array<string, int> $categories
     * @return array<string, int>
     */
    private function seedPlaces(array $users, array $categories): array
    {
        $now = now();
        $places = [
            [
                'name' => 'Central Park Commons',
                'category' => 'taman-kota',
                'user' => 'naufal@cityzen.test',
                'short_description' => 'Taman kota dengan jalur jalan kaki dan area duduk rindang.',
                'description' => 'Ruang terbuka hijau untuk olahraga ringan, piknik keluarga, dan kegiatan komunitas akhir pekan.',
                'address' => 'Jl. Taman Menteng No. 1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'latitude' => -6.1965000,
                'longitude' => 106.8325000,
                'google_maps_url' => 'https://maps.google.com/?q=Menteng+Jakarta',
            ],
            [
                'name' => 'Eco Zen Garden',
                'category' => 'ruang-komunitas',
                'user' => 'alya@cityzen.test',
                'short_description' => 'Kebun komunitas dengan area edukasi urban farming.',
                'description' => 'Warga dapat belajar kompos, menanam sayur, dan mengikuti workshop lingkungan.',
                'address' => 'Jl. Ciumbuleuit Hijau No. 12',
                'city' => 'Bandung',
                'province' => 'Jawa Barat',
                'latitude' => -6.8759000,
                'longitude' => 107.6044000,
                'google_maps_url' => 'https://maps.google.com/?q=Ciumbuleuit+Bandung',
            ],
            [
                'name' => 'Riverfront Walk',
                'category' => 'wisata',
                'user' => 'raka@cityzen.test',
                'short_description' => 'Koridor tepi sungai untuk jalan santai dan kegiatan bersih sungai.',
                'description' => 'Area publik tepi sungai dengan mural, bangku taman, dan titik pantau kebersihan.',
                'address' => 'Jl. Kali Code Selatan',
                'city' => 'Yogyakarta',
                'province' => 'DI Yogyakarta',
                'latitude' => -7.7956000,
                'longitude' => 110.3695000,
                'google_maps_url' => 'https://maps.google.com/?q=Kali+Code+Yogyakarta',
            ],
            [
                'name' => 'Solar Loop Plaza',
                'category' => 'fasilitas-umum',
                'user' => 'mira@cityzen.test',
                'short_description' => 'Plaza publik dengan panel surya dan akses pejalan kaki.',
                'description' => 'Simpul aktivitas publik yang menghubungkan halte, area duduk, dan kios UMKM.',
                'address' => 'Jl. Sudirman Kav. 44',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'latitude' => -6.2146000,
                'longitude' => 106.8218000,
                'google_maps_url' => 'https://maps.google.com/?q=Sudirman+Jakarta',
            ],
            [
                'name' => 'Lapangan Merdeka Active Park',
                'category' => 'tempat-olahraga',
                'user' => 'dimas@cityzen.test',
                'short_description' => 'Lapangan publik untuk olahraga pagi dan kegiatan komunitas.',
                'description' => 'Area olahraga terbuka dengan jogging track, lapangan serbaguna, dan ruang acara warga.',
                'address' => 'Jl. Balai Kota No. 1',
                'city' => 'Medan',
                'province' => 'Sumatera Utara',
                'latitude' => 3.5909000,
                'longitude' => 98.6778000,
                'google_maps_url' => 'https://maps.google.com/?q=Lapangan+Merdeka+Medan',
            ],
            [
                'name' => 'Kota Lama Food Court',
                'category' => 'kuliner',
                'user' => 'mira@cityzen.test',
                'short_description' => 'Sentra kuliner publik di kawasan kota lama.',
                'description' => 'Ruang kuliner terbuka yang mendukung UMKM lokal dan aktivitas wisata kota.',
                'address' => 'Jl. Letjen Suprapto No. 9',
                'city' => 'Semarang',
                'province' => 'Jawa Tengah',
                'latitude' => -6.9680000,
                'longitude' => 110.4278000,
                'google_maps_url' => 'https://maps.google.com/?q=Kota+Lama+Semarang',
            ],
        ];

        $placeIds = [];

        foreach ($places as $place) {
            $slug = Str::slug($place['name']);

            DB::table('places')->updateOrInsert(
                ['slug' => $slug],
                [
                    'user_id' => $users[$place['user']],
                    'category_id' => $categories[$place['category']],
                    'name' => $place['name'],
                    'short_description' => $place['short_description'],
                    'description' => $place['description'],
                    'address' => $place['address'],
                    'city' => $place['city'],
                    'province' => $place['province'],
                    'latitude' => $place['latitude'],
                    'longitude' => $place['longitude'],
                    'google_maps_url' => $place['google_maps_url'],
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $placeIds[$slug] = (int) DB::table('places')->where('slug', $slug)->value('id');
        }

        return $placeIds;
    }

    /**
     * @param array<string, int> $places
     * @param array<string, int> $users
     */
    private function seedPlacePhotos(array $places, array $users): void
    {
        $now = now();
        $photos = [
            ['place' => 'central-park-commons', 'user' => 'naufal@cityzen.test', 'image_path' => 'cityzen-dashboard/central-park.jpg', 'caption' => 'Area hijau utama Central Park Commons.'],
            ['place' => 'eco-zen-garden', 'user' => 'alya@cityzen.test', 'image_path' => 'cityzen-dashboard/eco-zen.jpg', 'caption' => 'Kebun komunitas Eco Zen Garden.'],
            ['place' => 'riverfront-walk', 'user' => 'raka@cityzen.test', 'image_path' => 'cityzen-dashboard/riverfront.jpg', 'caption' => 'Jalur pedestrian Riverfront Walk.'],
            ['place' => 'solar-loop-plaza', 'user' => 'mira@cityzen.test', 'image_path' => 'cityzen-dashboard/solar-loop.jpg', 'caption' => 'Area transit Solar Loop Plaza.'],
            ['place' => 'lapangan-merdeka-active-park', 'user' => 'dimas@cityzen.test', 'image_path' => 'cityzen-dashboard/skyline-greens.jpg', 'caption' => 'Lapangan olahraga publik Lapangan Merdeka.'],
            ['place' => 'kota-lama-food-court', 'user' => 'mira@cityzen.test', 'image_path' => 'cityzen-dashboard/urban-canopy.jpg', 'caption' => 'Koridor kuliner Kota Lama Food Court.'],
        ];

        foreach ($photos as $index => $photo) {
            DB::table('place_photos')->updateOrInsert(
                [
                    'place_id' => $places[$photo['place']],
                    'image_path' => $photo['image_path'],
                ],
                [
                    'user_id' => $users[$photo['user']],
                    'caption' => $photo['caption'],
                    'sort_order' => $index + 1,
                    'is_cover' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * @param array<string, int> $places
     * @param array<string, int> $users
     */
    private function seedReviews(array $places, array $users): void
    {
        $now = now();
        $reviews = [
            ['place' => 'central-park-commons', 'user' => 'alya@cityzen.test', 'rating' => 5, 'review' => 'Teduh dan nyaman untuk jalan sore, fasilitas duduknya cukup banyak.'],
            ['place' => 'eco-zen-garden', 'user' => 'naufal@cityzen.test', 'rating' => 5, 'review' => 'Program komunitasnya aktif dan cocok untuk edukasi lingkungan.'],
            ['place' => 'riverfront-walk', 'user' => 'dimas@cityzen.test', 'rating' => 4, 'review' => 'Spot mural menarik, perlu penerangan tambahan malam hari.'],
            ['place' => 'solar-loop-plaza', 'user' => 'raka@cityzen.test', 'rating' => 4, 'review' => 'Transitnya nyaman dan dekat area UMKM.'],
            ['place' => 'lapangan-merdeka-active-park', 'user' => 'mira@cityzen.test', 'rating' => 5, 'review' => 'Area olahraga luas, cocok untuk aktivitas komunitas pagi.'],
            ['place' => 'kota-lama-food-court', 'user' => 'alya@cityzen.test', 'rating' => 4, 'review' => 'Pilihan kuliner lokal bagus, perlu penambahan tempat sampah.'],
        ];

        foreach ($reviews as $review) {
            $reviewColumn = Schema::hasColumn('reviews', 'review_text') ? 'review_text' : 'review';

            DB::table('reviews')->updateOrInsert(
                [
                    'user_id' => $users[$review['user']],
                    'place_id' => $places[$review['place']],
                ],
                [
                    'rating' => $review['rating'],
                    $reviewColumn => $review['review'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * @param array<string, int> $places
     * @param array<string, int> $users
     */
    private function seedLikes(array $places, array $users): void
    {
        $now = now();
        $likes = [
            ['place' => 'central-park-commons', 'user' => 'alya@cityzen.test'],
            ['place' => 'eco-zen-garden', 'user' => 'naufal@cityzen.test'],
            ['place' => 'riverfront-walk', 'user' => 'dimas@cityzen.test'],
            ['place' => 'solar-loop-plaza', 'user' => 'raka@cityzen.test'],
            ['place' => 'lapangan-merdeka-active-park', 'user' => 'mira@cityzen.test'],
            ['place' => 'kota-lama-food-court', 'user' => 'admin@cityzen.test'],
        ];

        foreach ($likes as $like) {
            DB::table('likes')->updateOrInsert(
                [
                    'user_id' => $users[$like['user']],
                    'place_id' => $places[$like['place']],
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * @param array<string, int> $places
     * @param array<string, int> $users
     */
    private function seedBookmarks(array $places, array $users): void
    {
        $now = now();
        $bookmarks = [
            ['place' => 'central-park-commons', 'user' => 'naufal@cityzen.test'],
            ['place' => 'eco-zen-garden', 'user' => 'raka@cityzen.test'],
            ['place' => 'riverfront-walk', 'user' => 'alya@cityzen.test'],
            ['place' => 'solar-loop-plaza', 'user' => 'dimas@cityzen.test'],
            ['place' => 'lapangan-merdeka-active-park', 'user' => 'mira@cityzen.test'],
            ['place' => 'kota-lama-food-court', 'user' => 'admin@cityzen.test'],
        ];

        foreach ($bookmarks as $bookmark) {
            DB::table('bookmarks')->updateOrInsert(
                [
                    'user_id' => $users[$bookmark['user']],
                    'place_id' => $places[$bookmark['place']],
                ],
                [
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    /**
     * @return array{categories: array<string, int>, statuses: array<string, int>}
     */
    private function seedReportTaxonomy(): array
    {
        $now = now();
        $categories = [
            ['name' => 'Sampah', 'description' => 'Laporan kebersihan, tumpukan sampah, atau limbah.'],
            ['name' => 'Kerusakan fasilitas', 'description' => 'Laporan fasilitas rusak, aus, atau tidak bisa dipakai.'],
            ['name' => 'Keamanan', 'description' => 'Laporan penerangan, titik rawan, atau kondisi tidak aman.'],
            ['name' => 'Aksesibilitas', 'description' => 'Laporan hambatan akses untuk pejalan kaki, kursi roda, dan pengguna rentan.'],
            ['name' => 'Vandalisme', 'description' => 'Laporan coretan, perusakan, atau penyalahgunaan fasilitas.'],
            ['name' => 'Lainnya', 'description' => 'Laporan lain yang belum masuk kategori utama.'],
        ];
        $statuses = [
            ['name' => 'Pending', 'description' => 'Menunggu verifikasi admin.'],
            ['name' => 'Verified', 'description' => 'Laporan valid dan sudah diverifikasi.'],
            ['name' => 'Rejected', 'description' => 'Laporan ditolak karena kurang valid atau kurang bukti.'],
            ['name' => 'Resolved', 'description' => 'Masalah pada laporan sudah ditangani.'],
        ];

        $categoryIds = [];
        foreach ($categories as $category) {
            $slug = Str::slug($category['name']);
            $payload = [
                'name' => $category['name'],
                'description' => $category['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('report_categories', 'slug')) {
                $payload['slug'] = $slug;
            }

            if (Schema::hasColumn('report_categories', 'is_active')) {
                $payload['is_active'] = true;
            }

            DB::table('report_categories')->updateOrInsert(
                Schema::hasColumn('report_categories', 'slug') ? ['slug' => $slug] : ['name' => $category['name']],
                $payload
            );

            $categoryIds[$slug] = (int) DB::table('report_categories')
                ->where(Schema::hasColumn('report_categories', 'slug') ? 'slug' : 'name', Schema::hasColumn('report_categories', 'slug') ? $slug : $category['name'])
                ->value('id');
        }

        $statusIds = [];
        foreach ($statuses as $status) {
            $slug = Str::slug($status['name']);
            $payload = [
                'name' => $status['name'],
                'description' => $status['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (Schema::hasColumn('report_statuses', 'slug')) {
                $payload['slug'] = $slug;
            }

            if (Schema::hasColumn('report_statuses', 'is_active')) {
                $payload['is_active'] = true;
            }

            DB::table('report_statuses')->updateOrInsert(
                Schema::hasColumn('report_statuses', 'slug') ? ['slug' => $slug] : ['name' => $status['name']],
                $payload
            );

            $statusIds[$slug] = (int) DB::table('report_statuses')
                ->where(Schema::hasColumn('report_statuses', 'slug') ? 'slug' : 'name', Schema::hasColumn('report_statuses', 'slug') ? $slug : $status['name'])
                ->value('id');
        }

        return [
            'categories' => $categoryIds,
            'statuses' => $statusIds,
        ];
    }

    /**
     * @param array<string, int> $places
     * @param array<string, int> $users
     * @param array<string, int> $reportCategories
     * @param array<string, int> $reportStatuses
     */
    private function seedReports(array $places, array $users, array $reportCategories, array $reportStatuses): void
    {
        $now = now();
        $reports = [
            [
                'place' => 'central-park-commons',
                'user' => 'raka@cityzen.test',
                'category' => 'sampah',
                'description' => 'Tempat sampah dekat pintu utara penuh saat akhir pekan.',
                'status' => 'pending',
                'admin_note' => null,
                'photo' => 'cityzen-reports/central-park-trash.jpg',
            ],
            [
                'place' => 'riverfront-walk',
                'user' => 'alya@cityzen.test',
                'category' => 'keamanan',
                'description' => 'Beberapa lampu jalur pedestrian mati setelah pukul 19.00.',
                'status' => 'verified',
                'admin_note' => 'Laporan valid dan diteruskan ke pengelola kawasan.',
                'photo' => 'cityzen-reports/riverfront-lighting.jpg',
            ],
            [
                'place' => 'solar-loop-plaza',
                'user' => 'naufal@cityzen.test',
                'category' => 'aksesibilitas',
                'description' => 'Ramp kursi roda tertutup parkir motor di sisi timur.',
                'status' => 'resolved',
                'admin_note' => 'Area sudah dibersihkan oleh petugas.',
                'photo' => 'cityzen-reports/solar-loop-ramp.jpg',
            ],
            [
                'place' => 'eco-zen-garden',
                'user' => 'dimas@cityzen.test',
                'category' => 'kerusakan-fasilitas',
                'description' => 'Papan edukasi kompos mulai lapuk dan beberapa teks sulit dibaca.',
                'status' => 'pending',
                'admin_note' => null,
                'photo' => 'cityzen-reports/eco-zen-board.jpg',
            ],
            [
                'place' => 'lapangan-merdeka-active-park',
                'user' => 'mira@cityzen.test',
                'category' => 'vandalisme',
                'description' => 'Coretan ditemukan di dinding dekat jogging track sisi barat.',
                'status' => 'rejected',
                'admin_note' => 'Foto tidak cukup jelas, perlu laporan ulang dengan lokasi detail.',
                'photo' => 'cityzen-reports/merdeka-wall.jpg',
            ],
            [
                'place' => 'kota-lama-food-court',
                'user' => 'alya@cityzen.test',
                'category' => 'lainnya',
                'description' => 'Area antrean terlalu sempit saat jam ramai dan perlu pengaturan alur.',
                'status' => 'verified',
                'admin_note' => 'Diteruskan sebagai rekomendasi penataan area kuliner.',
                'photo' => 'cityzen-reports/kota-lama-queue.jpg',
            ],
        ];

        foreach ($reports as $report) {
            $verified = in_array($report['status'], ['verified', 'resolved'], true);
            $reportCategoryId = $reportCategories[$report['category']];
            $reportStatusId = $reportStatuses[$report['status']];

            DB::table('reports')->updateOrInsert(
                [
                    'user_id' => $users[$report['user']],
                    'place_id' => $places[$report['place']],
                    'report_category_id' => $reportCategoryId,
                ],
                [
                    'report_status_id' => $reportStatusId,
                    'description' => $report['description'],
                    'admin_note' => $report['admin_note'],
                    'verified_by' => $verified ? $users['admin@cityzen.test'] : null,
                    'verified_at' => $verified ? $now : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $reportId = (int) DB::table('reports')
                ->where('user_id', $users[$report['user']])
                ->where('place_id', $places[$report['place']])
                ->where('report_category_id', $reportCategoryId)
                ->value('id');

            DB::table('report_photos')->updateOrInsert(
                [
                    'report_id' => $reportId,
                    'image_path' => $report['photo'],
                ],
                [
                    'caption' => str($report['category'])->replace('-', ' ')->title().' report evidence',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function refreshPlaceCounters(): void
    {
        DB::table('places')->orderBy('id')->each(function ($place) {
            $reviews = DB::table('reviews')->where('place_id', $place->id);

            DB::table('places')
                ->where('id', $place->id)
                ->update([
                    'average_rating' => round((float) $reviews->avg('rating'), 2),
                    'reviews_count' => DB::table('reviews')->where('place_id', $place->id)->count(),
                    'likes_count' => DB::table('likes')->where('place_id', $place->id)->count(),
                    'bookmarks_count' => DB::table('bookmarks')->where('place_id', $place->id)->count(),
                    'reports_count' => DB::table('reports')->where('place_id', $place->id)->count(),
                    'updated_at' => now(),
                ]);
        });
    }
}
