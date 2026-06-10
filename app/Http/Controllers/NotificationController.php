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
                ->where('notifications.user_id', $userId)
                ->select([
                    'notifications.title',
                    'notifications.message',
                    'notifications.read_at',
                    'notifications.created_at',
                    'notification_types.name as type_name',
                    'actors.name as actor_name',
                ])
                ->orderByDesc('notifications.created_at')
                ->limit(30)
                ->get();
        }

        return view('notifications', [
            'notifications' => $notifications,
            'isAdmin' => CityZenAccess::isAdmin($request->session()->get('cityzen_user')),
        ]);
    }
}
