<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function index()
    {
        $places = \App\Models\Place::latest()->get();

        return view('places.index', compact('places'));
    }
}
