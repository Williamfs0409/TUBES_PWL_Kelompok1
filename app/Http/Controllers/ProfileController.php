<?php

namespace App\Http\Controllers;

use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProfileController extends Controller
{
    public function __invoke(Request $request)
    {
        $userId = (int) $request->session()->get('cityzen_user.id');
        $profile = Schema::hasTable('profiles')
            ? DB::table('profiles')->where('user_id', $userId)->first()
            : null;

        return view('profile', [
            'isAdmin' => CityZenAccess::isAdmin($request->session()->get('cityzen_user')),
            'stats' => CityZenAccess::profileStats($userId),
            'profile' => $profile,
        ]);
    }
}
