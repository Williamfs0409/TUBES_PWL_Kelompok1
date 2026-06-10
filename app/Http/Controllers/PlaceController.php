<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use App\Models\PlacePhoto;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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

                PlacePhoto::create([
                    'place_id' => $place->id,
                    'user_id' => $cityzenUser['id'] ?? null,
                    'image_path' => $imagePath,
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
