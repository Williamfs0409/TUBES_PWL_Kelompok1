<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InteractionController extends Controller
{
    public function like(Request $request, Place $place)
    {
        $userId = $request->session()->get('cityzen_user.id');

        if (! $userId) {
            return redirect('/login')->with('notice', 'Please login to like a place.');
        }

        $existing = DB::table('likes')
            ->where('user_id', $userId)
            ->where('place_id', $place->id)
            ->first();

        if ($existing) {
            DB::table('likes')->where('id', $existing->id)->delete();
        } else {
            DB::table('likes')->insert([
                'user_id' => $userId,
                'place_id' => $place->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $place->update([
            'likes_count' => DB::table('likes')->where('place_id', $place->id)->count(),
        ]);

        return back()->with('status', $existing ? 'Like removed.' : 'Place liked.');
    }

    public function bookmark(Request $request, Place $place)
    {
        $userId = $request->session()->get('cityzen_user.id');

        if (! $userId) {
            return redirect('/login')->with('notice', 'Please login to bookmark a place.');
        }

        $existing = DB::table('bookmarks')
            ->where('user_id', $userId)
            ->where('place_id', $place->id)
            ->first();

        if ($existing) {
            DB::table('bookmarks')->where('id', $existing->id)->delete();
        } else {
            DB::table('bookmarks')->insert([
                'user_id' => $userId,
                'place_id' => $place->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $place->update([
            'bookmarks_count' => DB::table('bookmarks')->where('place_id', $place->id)->count(),
        ]);

        return back()->with('status', $existing ? 'Bookmark removed.' : 'Place saved.');
    }

    public function review(Request $request, Place $place)
    {
        $userId = $request->session()->get('cityzen_user.id');

        if (! $userId) {
            return redirect('/login')->with('notice', 'Please login to review a place.');
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:500'],
        ]);

        $reviewColumn = Schema::hasColumn('reviews', 'review_text') ? 'review_text' : 'review';

        DB::table('reviews')->updateOrInsert(
            [
                'user_id' => $userId,
                'place_id' => $place->id,
            ],
            [
                'rating' => $data['rating'],
                $reviewColumn => $data['review'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $ratingStats = DB::table('reviews')
            ->where('place_id', $place->id)
            ->selectRaw('COUNT(*) as total, AVG(rating) as average')
            ->first();

        $place->update([
            'reviews_count' => (int) $ratingStats->total,
            'average_rating' => round((float) $ratingStats->average, 2),
        ]);

        return back()->with('status', 'Review submitted.');
    }
}
