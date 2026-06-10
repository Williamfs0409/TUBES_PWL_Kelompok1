<?php

namespace App\Http\Controllers;

use App\Support\CityZenAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->session()->get('cityzen_user.id');
        $notifications = collect();

        if (Schema::hasTable('notifications')) {
            $notifications = DB::table('notifications')
                ->leftJoin('notification_types', 'notification_types.id', '=', 'notifications.notification_type_id')
                ->leftJoin('users as actors', 'actors.id', '=', 'notifications.actor_id')
                ->leftJoin('profiles as actor_profiles', 'actor_profiles.user_id', '=', 'actors.id')
                ->leftJoin('places', function ($join) {
                    $join->on('places.id', '=', 'notifications.related_id')
                        ->where('notifications.related_table', '=', 'places');
                })
                ->where('notifications.user_id', $userId)
                ->select([
                    'notifications.id',
                    'notifications.actor_id',
                    'notifications.related_table',
                    'notifications.related_id',
                    'notifications.title',
                    'notifications.message',
                    'notifications.read_at',
                    'notifications.created_at',
                    'notifications.updated_at',
                    'notification_types.name as type_name',
                    'notification_types.slug as type_slug',
                    'actors.name as actor_name',
                    'actor_profiles.avatar_path as actor_avatar_path',
                    'places.name as place_name',
                ])
                ->orderByDesc('notifications.updated_at')
                ->limit(30)
                ->get();
        }

        return view('notifications', [
            'notifications' => $notifications,
            'isAdmin' => CityZenAccess::isAdmin($request->session()->get('cityzen_user')),
        ]);
    }
}
