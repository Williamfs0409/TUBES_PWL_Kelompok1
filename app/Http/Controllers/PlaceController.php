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
        $categories = Category::orderBy('name')->get();

        return view('places.create', compact('categories'));
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
        ]);

        $data['slug'] = Str::slug($data['name']).'-'.Str::random(6);
        $data['status'] = 'active';

        Place::create($data);

        return redirect('/places')->with('status', 'Place berhasil ditambahkan.');
    }
}