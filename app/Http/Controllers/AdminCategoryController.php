<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Place;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCategoryController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.categories.index', [
            'categories' => Category::orderBy('sort_order')->orderBy('name')->get(),
            'isSuperAdmin' => CityZenAccess::isSuperAdmin($request->session()->get('cityzen_user')),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:80'],
        ]);

        Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:80'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => (bool) ($data['is_active'] ?? false),
        ]);

        return back()->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if (Place::where('category_id', $category->id)->exists()) {
            return back()->withErrors(['category' => 'Kategori masih dipakai oleh places. Nonaktifkan saja jika belum bisa dihapus.']);
        }

        $category->delete();

        return back()->with('status', 'Kategori berhasil dihapus.');
    }
}
