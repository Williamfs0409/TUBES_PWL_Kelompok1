<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CityZenSeeder extends Seeder
{
    /**
     * Seed dummy CityZen data for local development and UI prototyping.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $users = $this->seedUsers();
            $categories = $this->seedCategories();
            $places = $this->seedPlaces($users, $categories);

            $this->seedPlacePhotos($places, $users);
            $this->seedReviews($places, $users);
            $this->seedLikes($places, $users);
            $this->seedBookmarks($places, $users);
            $this->seedReports($places, $users);
            $this->refreshPlaceCounters();
        });
    }

    /**
     * @return array<string, int>
     */
    private function seedUsers(): array
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
            $user = User::query()->updateOrCreate(
                ['email' => $dummyUser['email']],
                [
                    'name' => $dummyUser['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => $now,
                ]
            );

            DB::table('profiles')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'username' => $dummyUser['username'],
                    'avatar_path' => null,
                    'city' => $dummyUser['city'],
                    'bio' => $dummyUser['bio'],
                    'contribution_count' => $dummyUser['contribution_count'],
                    'current_badge' => $dummyUser['current_badge'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            $users[$dummyUser['email']] = $user->id;
        }

        return $users;
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
            ['name' => 'Edukasi', 'description' => 'Tempat publik yang mendukung kegiatan belajar.', 'icon' => 'book-open'],
            ['name' => 'Transportasi Publik', 'description' => 'Simpul transit dan fasilitas mobilitas publik.', 'icon' => 'bus'],
            ['name' => 'Lainnya', 'description' => 'Kategori tambahan untuk ruang publik lain.', 'icon' => 'circle-ellipsis'],
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
                'category' => 'transportasi-publik',
                'user' => 'naufal@cityzen.test',
                'short_description' => 'Plaza transit dengan panel surya dan akses pejalan kaki.',
                'description' => 'Simpul transit publik yang menghubungkan halte, area duduk, dan kios UMKM.',
                'address' => 'Jl. Sudirman Kav. 44',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
                'latitude' => -6.2146000,
                'longitude' => 106.8218000,
                'google_maps_url' => 'https://maps.google.com/?q=Sudirman+Jakarta',
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
            ['place' => 'solar-loop-plaza', 'user' => 'naufal@cityzen.test', 'image_path' => 'cityzen-dashboard/solar-loop.jpg', 'caption' => 'Area transit Solar Loop Plaza.'],
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
            ['place' => 'central-park-commons', 'user' => 'raka@cityzen.test', 'rating' => 4, 'review' => 'Akses pejalan kaki bagus, tapi tempat sampah perlu ditambah.'],
            ['place' => 'eco-zen-garden', 'user' => 'naufal@cityzen.test', 'rating' => 5, 'review' => 'Program komunitasnya aktif dan cocok untuk edukasi lingkungan.'],
            ['place' => 'riverfront-walk', 'user' => 'alya@cityzen.test', 'rating' => 4, 'review' => 'Spot mural menarik, perlu penerangan tambahan malam hari.'],
            ['place' => 'solar-loop-plaza', 'user' => 'raka@cityzen.test', 'rating' => 4, 'review' => 'Transitnya nyaman dan dekat area UMKM.'],
        ];

        foreach ($reviews as $review) {
            DB::table('reviews')->updateOrInsert(
                [
                    'user_id' => $users[$review['user']],
                    'place_id' => $places[$review['place']],
                ],
                [
                    'rating' => $review['rating'],
                    'review' => $review['review'],
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
            ['place' => 'central-park-commons', 'user' => 'raka@cityzen.test'],
            ['place' => 'eco-zen-garden', 'user' => 'naufal@cityzen.test'],
            ['place' => 'eco-zen-garden', 'user' => 'raka@cityzen.test'],
            ['place' => 'riverfront-walk', 'user' => 'naufal@cityzen.test'],
            ['place' => 'solar-loop-plaza', 'user' => 'alya@cityzen.test'],
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
            ['place' => 'solar-loop-plaza', 'user' => 'naufal@cityzen.test'],
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
     * @param array<string, int> $places
     * @param array<string, int> $users
     */
    private function seedReports(array $places, array $users): void
    {
        $now = now();
        $reports = [
            [
                'place' => 'central-park-commons',
                'user' => 'raka@cityzen.test',
                'category' => 'Sampah',
                'description' => 'Tempat sampah dekat pintu utara penuh saat akhir pekan.',
                'status' => 'pending',
                'admin_note' => null,
                'photo' => 'cityzen-reports/central-park-trash.jpg',
            ],
            [
                'place' => 'riverfront-walk',
                'user' => 'alya@cityzen.test',
                'category' => 'Keamanan',
                'description' => 'Beberapa lampu jalur pedestrian mati setelah pukul 19.00.',
                'status' => 'verified',
                'admin_note' => 'Laporan valid dan diteruskan ke pengelola kawasan.',
                'photo' => 'cityzen-reports/riverfront-lighting.jpg',
            ],
            [
                'place' => 'solar-loop-plaza',
                'user' => 'naufal@cityzen.test',
                'category' => 'Aksesibilitas',
                'description' => 'Ramp kursi roda tertutup parkir motor di sisi timur.',
                'status' => 'resolved',
                'admin_note' => 'Area sudah dibersihkan oleh petugas.',
                'photo' => 'cityzen-reports/solar-loop-ramp.jpg',
            ],
        ];

        foreach ($reports as $report) {
            $verified = in_array($report['status'], ['verified', 'resolved'], true);

            DB::table('reports')->updateOrInsert(
                [
                    'user_id' => $users[$report['user']],
                    'place_id' => $places[$report['place']],
                    'category' => $report['category'],
                ],
                [
                    'description' => $report['description'],
                    'status' => $report['status'],
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
                ->where('category', $report['category'])
                ->value('id');

            DB::table('report_photos')->updateOrInsert(
                [
                    'report_id' => $reportId,
                    'image_path' => $report['photo'],
                ],
                [
                    'caption' => $report['category'].' report evidence',
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
