<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use App\Models\PlacePhoto;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PlaceController extends Controller
{
    private function ensureDefaultCategories(): void
    {
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
            $defaults = ['name' => $name];

            if (Schema::hasColumn('categories', 'sort_order')) {
                $defaults['sort_order'] = $index + 1;
            }

            if (Schema::hasColumn('categories', 'is_active')) {
                $defaults['is_active'] = true;
            }

            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                $defaults
            );
        });
    }

    public function index()
    {
        if (! session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to manage places.');
        }

        $places = Place::latest()->get();

        return view('places.index', compact('places'));
    }

    public function create()
    {
        if (! session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to add a place.');
        }

        $this->ensureDefaultCategories();

        $categories = Category::query()
            ->when(Schema::hasColumn('categories', 'is_active'), fn ($query) => $query->where('is_active', true))
            ->when(Schema::hasColumn('categories', 'sort_order'), fn ($query) => $query->orderBy('sort_order'))
            ->orderBy('name')
            ->get();

        return view('places.create', compact('categories'));
    }

    public function show(Place $place)
    {
        $cityzenUser = session('cityzen_user');
        $userId = $cityzenUser['id'] ?? null;

        if ($place->status !== 'active' && (int) $place->user_id !== (int) $userId && ! CityZenAccess::isAdmin($cityzenUser)) {
            abort(404);
        }

        $place->load(['category', 'user', 'coverPhoto', 'photos']);

        $compactNumber = function (int $number): string {
            if ($number >= 1000) {
                return rtrim(rtrim(number_format($number / 1000, 1), '0'), '.').'k';
            }

            return (string) $number;
        };

        $initials = function (?string $name): string {
            $parts = collect(explode(' ', trim($name ?: 'CityZen User')))->filter()->values();

            return $parts->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'CZ';
        };

        $reviewColumn = Schema::hasColumn('reviews', 'review_text') ? 'review_text' : 'review';
        $likesCount = Schema::hasTable('likes') ? DB::table('likes')->where('place_id', $place->id)->count() : (int) $place->likes_count;
        $bookmarksCount = Schema::hasTable('bookmarks') ? DB::table('bookmarks')->where('place_id', $place->id)->count() : (int) $place->bookmarks_count;
        $repostsCount = Schema::hasTable('reposts') ? DB::table('reposts')->where('place_id', $place->id)->count() : 0;
        $reportsCount = Schema::hasTable('reports') ? DB::table('reports')->where('place_id', $place->id)->count() : (int) $place->reports_count;
        $reviewStats = Schema::hasTable('reviews')
            ? DB::table('reviews')->where('place_id', $place->id)->selectRaw('COUNT(*) as total, AVG(rating) as average')->first()
            : null;

        $author = $place->user?->name ?: 'CityZen Citizen';
        $avatarPath = Schema::hasTable('profiles')
            ? DB::table('profiles')->where('user_id', $place->user_id)->value('avatar_path')
            : null;
        $location = collect([$place->city, $place->province])->filter()->implode(', ');
        $description = $place->short_description ?: $place->description;
        $liked = Schema::hasTable('likes') && $userId ? DB::table('likes')->where('user_id', $userId)->where('place_id', $place->id)->exists() : false;
        $bookmarked = Schema::hasTable('bookmarks') && $userId ? DB::table('bookmarks')->where('user_id', $userId)->where('place_id', $place->id)->exists() : false;
        $reposted = Schema::hasTable('reposts') && $userId ? DB::table('reposts')->where('user_id', $userId)->where('place_id', $place->id)->exists() : false;

        $post = [
            'id' => $place->id,
            'author_id' => $place->user_id,
            'author' => $author,
            'handle' => '@'.str($author)->slug('_'),
            'time' => $place->created_at ? Carbon::parse($place->created_at)->diffForHumans(null, true).' ago' : 'baru',
            'timestamp' => $place->created_at ? Carbon::parse($place->created_at)->format('g:i A - M j, Y') : null,
            'avatar' => $initials($author),
            'avatar_image' => $avatarPath,
            'lead' => $place->name,
            'text' => trim($description.' '.($location ? 'Lokasi: '.$location.'.' : '')),
            'image' => $place->coverPhoto || $place->image,
            'image_alt' => $place->name,
            'badge' => str($place->category?->name ?: 'Public Space')->studly()->toString(),
            'category' => $place->category?->name ?: 'Public Space',
            'comments' => $compactNumber((int) ($reviewStats->total ?? 0)),
            'reposts' => $compactNumber((int) $repostsCount),
            'likes' => $compactNumber((int) $likesCount),
            'bookmarks' => $compactNumber((int) $bookmarksCount),
            'reports' => $compactNumber((int) $reportsCount),
            'rating' => number_format((float) ($reviewStats->average ?? $place->average_rating), 1),
            'liked' => $liked,
            'bookmarked' => $bookmarked,
            'reposted' => $reposted,
            'owned' => (int) $place->user_id === (int) $userId,
        ];

        $photos = $place->photos
            ->sortBy('sort_order')
            ->values()
            ->map(fn (PlacePhoto $photo) => [
                'id' => $photo->id,
                'caption' => $photo->caption ?: $place->name,
            ]);

        $reviews = Schema::hasTable('reviews')
            ? DB::table('reviews')
                ->leftJoin('users', 'users.id', '=', 'reviews.user_id')
                ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
                ->where('reviews.place_id', $place->id)
                ->orderByDesc('reviews.updated_at')
                ->limit(25)
                ->get([
                    'reviews.id',
                    'reviews.rating',
                    "reviews.$reviewColumn as review_text",
                    'reviews.updated_at',
                    'users.name as user_name',
                    'users.id as user_id',
                    'profiles.avatar_path',
                ])
                ->map(fn ($review) => [
                    'user_id' => $review->user_id,
                    'author' => $review->user_name ?: 'CityZen Citizen',
                    'handle' => '@'.str($review->user_name ?: 'cityzen_citizen')->slug('_'),
                    'avatar' => $initials($review->user_name),
                    'avatar_image' => $review->avatar_path,
                    'rating' => (int) $review->rating,
                    'text' => $review->review_text ?: 'Memberikan rating untuk tempat ini.',
                    'time' => $review->updated_at ? Carbon::parse($review->updated_at)->diffForHumans(null, true).' ago' : 'baru',
                ])
            : collect();

        $related = Schema::hasTable('places')
            ? DB::table('places')
                ->leftJoin('categories', 'categories.id', '=', 'places.category_id')
                ->where('places.status', 'active')
                ->whereNull('places.deleted_at')
                ->where('places.id', '!=', $place->id)
                ->orderByRaw('(places.likes_count + places.reviews_count + places.reports_count) DESC')
                ->limit(4)
                ->get(['places.id', 'places.name', 'places.likes_count', 'places.reviews_count', 'categories.name as category_name'])
            : collect();

        return view('places.show', [
            'post' => $post,
            'photos' => $photos,
            'reviews' => $reviews,
            'related' => $related,
            'isAdmin' => CityZenAccess::isAdmin($cityzenUser),
        ]);
    }

    public function image(Place $place)
    {
        $coverPhoto = $place->coverPhoto()->first();

        if ($coverPhoto?->image_data && $coverPhoto?->image_mime) {
            return response(base64_decode($coverPhoto->image_data), 200, [
                'Content-Type' => $coverPhoto->image_mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $imagePath = $coverPhoto?->image_path ?: $place->image;
        $relativePath = $imagePath ? str($imagePath)->after('storage/')->toString() : null;

        if ($relativePath && Storage::disk('public')->exists($relativePath)) {
            return response()->file(Storage::disk('public')->path($relativePath), [
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }

        $title = e($place->name);
        $category = e($place->category?->name ?? 'CityZen public space');
        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 650" role="img" aria-label="{$title}">
  <defs>
    <linearGradient id="sky" x1="0" x2="1" y1="0" y2="1">
      <stop offset="0" stop-color="#d8f3c9"/>
      <stop offset="0.55" stop-color="#8fbc83"/>
      <stop offset="1" stop-color="#173522"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="650" rx="28" fill="url(#sky)"/>
  <rect y="430" width="1200" height="220" fill="#102919" opacity=".88"/>
  <g opacity=".92">
    <rect x="150" y="220" width="78" height="210" fill="#f5fff1"/>
    <rect x="310" y="170" width="92" height="260" fill="#f5fff1"/>
    <rect x="500" y="250" width="76" height="180" fill="#f5fff1"/>
    <rect x="740" y="205" width="88" height="225" fill="#f5fff1"/>
    <rect x="930" y="260" width="76" height="170" fill="#f5fff1"/>
  </g>
  <g fill="#347c3d">
    <circle cx="110" cy="360" r="64"/>
    <circle cx="245" cy="330" r="66"/>
    <circle cx="405" cy="365" r="76"/>
    <circle cx="590" cy="335" r="68"/>
    <circle cx="745" cy="365" r="76"/>
    <circle cx="915" cy="325" r="68"/>
    <circle cx="1080" cy="360" r="70"/>
  </g>
  <text x="86" y="520" fill="#fffef7" font-family="Inter, Arial, sans-serif" font-size="54" font-weight="800">{$title}</text>
  <text x="88" y="570" fill="#dff7d8" font-family="Inter, Arial, sans-serif" font-size="25" font-weight="600">{$category} · CityZen public space</text>
</svg>
SVG;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }

    public function photo(Place $place, PlacePhoto $photo)
    {
        abort_unless((int) $photo->place_id === (int) $place->id, 404);

        if ($photo->image_data && $photo->image_mime) {
            return response(base64_decode($photo->image_data), 200, [
                'Content-Type' => $photo->image_mime,
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $relativePath = $photo->image_path ? str($photo->image_path)->after('storage/')->toString() : null;

        if ($relativePath && Storage::disk('public')->exists($relativePath)) {
            return response()->file(Storage::disk('public')->path($relativePath), [
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }

        abort(404);
    }

    public function store(Request $request)
    {
        if (! $request->session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to add a place.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'google_maps_url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'photos' => ['nullable', 'array', 'max:6'],
            'photos.*' => ['image', 'max:4096'],
        ]);

        $cityzenUser = $request->session()->get('cityzen_user');

        $data['user_id'] = $cityzenUser['id'] ?? null;
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['status'] = 'active';

        $photos = $request->file('photos', []);
        unset($data['photos']);

        DB::transaction(function () use ($data, $photos, $cityzenUser) {
            $place = Place::create($data);

            foreach ($photos as $index => $photo) {
                $imagePath = 'storage/'.$photo->store('places', 'public');
                $imageData = base64_encode(file_get_contents($photo->getRealPath()));
                $imageMime = $photo->getMimeType();

                PlacePhoto::create([
                    'place_id' => $place->id,
                    'user_id' => $cityzenUser['id'] ?? null,
                    'image_path' => $imagePath,
                    'image_mime' => $imageMime,
                    'image_data' => $imageData,
                    'caption' => $place->name,
                    'sort_order' => $index + 1,
                    'is_cover' => $index === 0,
                ]);

                if ($index === 0 && Schema::hasColumn('places', 'image')) {
                    $place->update(['image' => $imagePath]);
                }
            }
        });

        return redirect('/dashboard')->with('status', 'Tempat publik berhasil ditambahkan.');
    }


    public function edit(Place $place)
    {
        if (! session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to edit a place.');
        }

        $categories = Category::query()
            ->when(Schema::hasColumn('categories', 'is_active'), fn ($query) => $query->where('is_active', true))
            ->when(Schema::hasColumn('categories', 'sort_order'), fn ($query) => $query->orderBy('sort_order'))
            ->orderBy('name')
            ->get();

        return view('places.edit', compact('place', 'categories'));
    }

    public function update(Request $request, Place $place)
    {
        if (! $request->session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to update a place.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'google_maps_url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
        ]);

        $place->update($data);

        return redirect('/places')->with('status', 'Place berhasil diperbarui.');
    }

    public function destroy(Request $request, Place $place)
    {
        if (! $request->session()->has('cityzen_user')) {
            return redirect('/login')->with('notice', 'Please login to delete a place.');
        }

        $cityzenUser = $request->session()->get('cityzen_user');
        $isOwner = (int) $place->user_id === (int) ($cityzenUser['id'] ?? 0);

        if (! $isOwner && ! CityZenAccess::isAdmin($cityzenUser)) {
            abort(403, 'Kamu hanya bisa menghapus post yang kamu buat sendiri.');
        }

        $place->delete();

        return redirect('/dashboard')->with('status', 'Post berhasil dihapus dari feed CityZen.');
    }
}
