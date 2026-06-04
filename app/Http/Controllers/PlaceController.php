<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlaceController extends Controller
{
    public function index()
    {
        $places = Place::latest()->get();

        return view('places.index', compact('places'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)
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

        $cityzenUser = $request->session()->get('cityzen_user');

        $data['user_id'] = $cityzenUser['id'] ?? null;
        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['status'] = 'active';

        Place::create($data);

        return redirect('/dashboard')->with('status', 'Tempat publik berhasil ditambahkan.');
    }


    public function edit(Place $place)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('places.edit', compact('place', 'categories'));
    }

    public function update(Request $request, Place $place)
    {
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

    public function destroy(Place $place)
    {
        $place->delete();

        return redirect('/places')->with('status', 'Place berhasil dihapus.');
    }
}
