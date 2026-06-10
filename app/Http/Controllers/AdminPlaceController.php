<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Support\CityZenAccess;
use Illuminate\Http\Request;

class AdminPlaceController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.places.index', [
            'places' => Place::with(['category', 'user'])->latest()->get(),
            'isSuperAdmin' => CityZenAccess::isSuperAdmin($request->session()->get('cityzen_user')),
        ]);
    }

    public function updateStatus(Request $request, Place $place)
    {
        $data = $request->validate([
            'status' => ['required', 'in:active,hidden,rejected'],
        ]);

        $place->update(['status' => $data['status']]);

        return back()->with('status', 'Status place berhasil diperbarui.');
    }

    public function destroy(Place $place)
    {
        $place->delete();

        return back()->with('status', 'Postingan place berhasil dihapus.');
    }
}
