<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use App\Models\PlacePhoto;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        return redirect('/dashboard')->with('status', $place->name.' dibuka dari feed CityZen.');
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
