<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CityZenBadges
{
    public static function definitions(): array
    {
        return [
            [
                'slug' => 'first-place',
                'name' => 'First Place',
                'description' => 'Kontribusi tempat publik pertama berhasil masuk ke CityZen.',
                'requirement_text' => '1 tempat publik',
                'metric' => 'places',
                'target' => 1,
            ],
            [
                'slug' => 'urban-explorer',
                'name' => 'Urban Explorer',
                'description' => 'Aktif memetakan ruang publik yang bisa ditemukan warga lain.',
                'requirement_text' => '5 tempat publik',
                'metric' => 'places',
                'target' => 5,
            ],
            [
                'slug' => 'city-mapper',
                'name' => 'City Mapper',
                'description' => 'Kontributor kuat untuk database ruang publik CityZen.',
                'requirement_text' => '15 tempat publik',
                'metric' => 'places',
                'target' => 15,
            ],
            [
                'slug' => 'first-review',
                'name' => 'First Reviewer',
                'description' => 'Review pertama membantu warga memahami kualitas tempat.',
                'requirement_text' => '1 review',
                'metric' => 'reviews',
                'target' => 1,
            ],
            [
                'slug' => 'community-voice',
                'name' => 'Community Voice',
                'description' => 'Memberi banyak masukan untuk kualitas ruang publik.',
                'requirement_text' => '10 review',
                'metric' => 'reviews',
                'target' => 10,
            ],
            [
                'slug' => 'first-alert',
                'name' => 'First Alert',
                'description' => 'Laporan valid pertama membantu admin memantau masalah kota.',
                'requirement_text' => '1 laporan verified/resolved',
                'metric' => 'valid_reports',
                'target' => 1,
            ],
            [
                'slug' => 'civic-guardian',
                'name' => 'Civic Guardian',
                'description' => 'Konsisten menjaga fasilitas publik melalui laporan valid.',
                'requirement_text' => '5 laporan verified/resolved',
                'metric' => 'valid_reports',
                'target' => 5,
            ],
            [
                'slug' => 'helpful-post',
                'name' => 'Helpful Post',
                'description' => 'Postingan tempatmu mulai membantu dan menarik perhatian warga.',
                'requirement_text' => '10 likes diterima',
                'metric' => 'received_likes',
                'target' => 10,
            ],
            [
                'slug' => 'community-favorite',
                'name' => 'Community Favorite',
                'description' => 'Tempat yang kamu bagikan sering disimpan warga lain.',
                'requirement_text' => '10 bookmarks diterima',
                'metric' => 'received_bookmarks',
                'target' => 10,
            ],
        ];
    }

    public static function seedDefinitions(): void
    {
        if (! Schema::hasTable('badges')) {
            return;
        }

        foreach (self::definitions() as $badge) {
            DB::table('badges')->updateOrInsert(
                ['slug' => $badge['slug']],
                [
                    'name' => $badge['name'],
                    'slug' => $badge['slug'],
                    'description' => $badge['description'],
                    'requirement_text' => $badge['requirement_text'],
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    public static function progressForUser(int $userId): array
    {
        $validStatusIds = Schema::hasTable('report_statuses')
            ? DB::table('report_statuses')
                ->whereIn('slug', ['verified', 'resolved'])
                ->orWhereIn(DB::raw('LOWER(name)'), ['verified', 'resolved'])
                ->pluck('id')
            : collect();

        $placeIds = Schema::hasTable('places')
            ? DB::table('places')->where('user_id', $userId)->whereNull('deleted_at')->pluck('id')
            : collect();

        return [
            'places' => $placeIds->count(),
            'reviews' => Schema::hasTable('reviews') ? DB::table('reviews')->where('user_id', $userId)->count() : 0,
            'valid_reports' => Schema::hasTable('reports') && $validStatusIds->isNotEmpty()
                ? DB::table('reports')
                    ->where('user_id', $userId)
                    ->whereIn('report_status_id', $validStatusIds)
                    ->count()
                : 0,
            'received_likes' => Schema::hasTable('likes') && $placeIds->isNotEmpty()
                ? DB::table('likes')->whereIn('place_id', $placeIds)->where('user_id', '!=', $userId)->count()
                : 0,
            'received_bookmarks' => Schema::hasTable('bookmarks') && $placeIds->isNotEmpty()
                ? DB::table('bookmarks')->whereIn('place_id', $placeIds)->where('user_id', '!=', $userId)->count()
                : 0,
        ];
    }

    public static function evaluateForUser(?int $userId, ?string $sourceTable = null, ?int $sourceId = null): Collection
    {
        if (! $userId || ! Schema::hasTable('badges') || ! Schema::hasTable('badge_user')) {
            return collect();
        }

        self::seedDefinitions();

        $progress = self::progressForUser($userId);
        $earned = collect();

        foreach (self::definitions() as $definition) {
            $value = (int) ($progress[$definition['metric']] ?? 0);

            if ($value < (int) $definition['target']) {
                continue;
            }

            $badge = DB::table('badges')->where('slug', $definition['slug'])->first();

            if (! $badge) {
                continue;
            }

            $alreadyEarned = DB::table('badge_user')
                ->where('user_id', $userId)
                ->where('badge_id', $badge->id)
                ->exists();

            if ($alreadyEarned) {
                continue;
            }

            DB::table('badge_user')->insert([
                'user_id' => $userId,
                'badge_id' => $badge->id,
                'source_table' => $sourceTable,
                'source_id' => $sourceId,
                'progress_snapshot' => json_encode($progress),
                'earned_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            self::syncProfileBadge($userId, (string) $badge->name);
            self::notifyBadgeEarned($userId, $badge);

            $earned->push($badge);
        }

        return $earned;
    }

    public static function earnedForUser(int $userId): Collection
    {
        if (! Schema::hasTable('badges') || ! Schema::hasTable('badge_user')) {
            return collect();
        }

        return DB::table('badge_user')
            ->join('badges', 'badges.id', '=', 'badge_user.badge_id')
            ->where('badge_user.user_id', $userId)
            ->orderByDesc('badge_user.earned_at')
            ->get([
                'badges.id',
                'badges.name',
                'badges.slug',
                'badges.description',
                'badges.requirement_text',
                'badge_user.earned_at',
                'badge_user.progress_snapshot',
            ]);
    }

    public static function nextForUser(int $userId): Collection
    {
        $earnedSlugs = self::earnedForUser($userId)->pluck('slug');
        $progress = self::progressForUser($userId);

        return collect(self::definitions())
            ->reject(fn ($badge) => $earnedSlugs->contains($badge['slug']))
            ->map(function ($badge) use ($progress) {
                $current = (int) ($progress[$badge['metric']] ?? 0);
                $badge['current'] = $current;
                $badge['percent'] = min(100, (int) floor(($current / max(1, (int) $badge['target'])) * 100));

                return (object) $badge;
            })
            ->sortBy([
                ['percent', 'desc'],
                ['target', 'asc'],
            ])
            ->take(3)
            ->values();
    }

    private static function syncProfileBadge(int $userId, string $badgeName): void
    {
        if (! Schema::hasTable('profiles') || ! Schema::hasColumn('profiles', 'current_badge')) {
            return;
        }

        $profileExists = DB::table('profiles')->where('user_id', $userId)->exists();

        if ($profileExists) {
            DB::table('profiles')->where('user_id', $userId)->update([
                'current_badge' => $badgeName,
                'updated_at' => now(),
            ]);

            return;
        }

        $email = Schema::hasTable('users')
            ? DB::table('users')->where('id', $userId)->value('email')
            : null;

        DB::table('profiles')->insert([
            'user_id' => $userId,
            'username' => Str::of($email ?: 'cityzen')->before('@')->slug('_')->limit(32, '').'_'.$userId,
            'current_badge' => $badgeName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private static function notifyBadgeEarned(int $userId, object $badge): void
    {
        if (! Schema::hasTable('notifications') || ! Schema::hasTable('notification_types')) {
            return;
        }

        $typeId = DB::table('notification_types')->where('slug', 'badge')->value('id');

        if (! $typeId) {
            $typeId = DB::table('notification_types')->insertGetId([
                'name' => 'Badge',
                'slug' => 'badge',
                'description' => 'Badge and gamification updates.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('notifications')->insert([
            'user_id' => $userId,
            'actor_id' => null,
            'notification_type_id' => $typeId,
            'title' => 'Badge unlocked: '.$badge->name,
            'message' => 'Kamu mendapatkan badge '.$badge->name.'. '.Str::of((string) ($badge->description ?? ''))->trim(),
            'related_table' => 'badges',
            'related_id' => $badge->id,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
