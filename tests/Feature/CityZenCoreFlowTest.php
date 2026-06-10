<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Place;
use App\Models\ReportCategory;
use App\Models\User;
use App\Support\CityZenAccess;
use Database\Seeders\CityZenFoundationSeeder;
use Database\Seeders\CityZenSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CityZenCoreFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_user_can_login_and_open_dashboard(): void
    {
        $this->seed(CityZenSeeder::class);

        $this->post('/login', [
            'email' => 'naufal@cityzen.test',
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->withSession([
            'cityzen_user' => CityZenAccess::sessionPayload(User::where('email', 'naufal@cityzen.test')->firstOrFail()),
        ])->get('/dashboard')
            ->assertOk()
            ->assertSee('CityZen', false);
    }

    public function test_user_can_create_place_and_interact_with_it(): void
    {
        $this->seed(CityZenSeeder::class);

        $user = User::where('email', 'naufal@cityzen.test')->firstOrFail();
        $category = Category::firstOrFail();
        $session = ['cityzen_user' => CityZenAccess::sessionPayload($user)];

        $this->withSession($session)
            ->post('/places', [
                'name' => 'Testing Community Park',
                'category_id' => $category->id,
                'short_description' => 'A clean test park for automated checks.',
                'description' => 'A clean public park created from the feature test.',
                'address' => 'Jl. Testing No. 1',
                'city' => 'Jakarta',
                'province' => 'DKI Jakarta',
            ])
            ->assertRedirect('/dashboard');

        $place = Place::where('name', 'Testing Community Park')->firstOrFail();

        $this->withSession($session)->post(route('places.like', $place))->assertRedirect();
        $this->withSession($session)->post(route('places.bookmark', $place))->assertRedirect();
        $this->withSession($session)->post(route('places.review', $place), [
            'rating' => 5,
            'review' => 'Good automated test review.',
        ])->assertRedirect();

        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'place_id' => $place->id]);
        $this->assertDatabaseHas('bookmarks', ['user_id' => $user->id, 'place_id' => $place->id]);
        $this->assertDatabaseHas('reviews', ['user_id' => $user->id, 'place_id' => $place->id, 'rating' => 5]);
    }

    public function test_user_can_delete_only_their_own_place(): void
    {
        $this->seed(CityZenSeeder::class);

        $owner = User::where('email', 'naufal@cityzen.test')->firstOrFail();
        $otherUser = User::where('email', 'alya@cityzen.test')->firstOrFail();
        $category = Category::firstOrFail();

        $place = Place::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Owner Managed Pocket Park',
            'slug' => 'owner-managed-pocket-park',
            'short_description' => 'A place owned by the test user.',
            'description' => 'A place owned by the test user.',
            'address' => 'Jl. Owner No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'status' => 'active',
        ]);

        $this->withSession(['cityzen_user' => CityZenAccess::sessionPayload($otherUser)])
            ->delete(route('places.destroy', $place))
            ->assertForbidden();

        $this->assertFalse($place->fresh()->trashed());

        $this->withSession(['cityzen_user' => CityZenAccess::sessionPayload($owner)])
            ->delete(route('places.destroy', $place))
            ->assertRedirect('/dashboard');

        $this->assertSoftDeleted('places', ['id' => $place->id]);
    }

    public function test_like_and_repost_create_notifications_for_place_owner(): void
    {
        $this->seed(CityZenSeeder::class);

        $owner = User::where('email', 'naufal@cityzen.test')->firstOrFail();
        $actor = User::where('email', 'alya@cityzen.test')->firstOrFail();
        $category = Category::firstOrFail();

        $place = Place::create([
            'user_id' => $owner->id,
            'category_id' => $category->id,
            'name' => 'Notification Test Park',
            'slug' => 'notification-test-park',
            'short_description' => 'A place used for notification checks.',
            'description' => 'A place used for notification checks.',
            'address' => 'Jl. Notify No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'status' => 'active',
        ]);

        $actorSession = ['cityzen_user' => CityZenAccess::sessionPayload($actor)];

        $this->withSession($actorSession)->post(route('places.like', $place))->assertRedirect();
        $this->withSession($actorSession)->post(route('places.repost', $place))->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'actor_id' => $actor->id,
            'related_table' => 'places',
            'related_id' => $place->id,
            'title' => $actor->name.' liked your post',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $owner->id,
            'actor_id' => $actor->id,
            'related_table' => 'places',
            'related_id' => $place->id,
            'title' => $actor->name.' reposted your post',
        ]);
    }

    public function test_user_can_report_place_and_admin_can_moderate_it(): void
    {
        $this->seed(CityZenSeeder::class);

        $user = User::where('email', 'naufal@cityzen.test')->firstOrFail();
        $admin = User::where('email', 'admin@cityzen.test')->firstOrFail();
        $place = Place::firstOrFail();
        $category = ReportCategory::firstOrFail();

        $this->withSession(['cityzen_user' => CityZenAccess::sessionPayload($user)])
            ->post(route('reports.store', $place), [
                'report_category_id' => $category->id,
                'description' => 'Automated report evidence for moderation flow.',
            ])
            ->assertRedirect('/dashboard');

        $reportId = DB::table('reports')
            ->where('user_id', $user->id)
            ->where('place_id', $place->id)
            ->latest('id')
            ->value('id');

        $verifiedStatusId = DB::table('report_statuses')->where('slug', 'verified')->value('id');

        $this->withSession(['cityzen_user' => CityZenAccess::sessionPayload($admin)])
            ->post(route('admin.reports.status', $reportId), [
                'report_status_id' => $verifiedStatusId,
                'admin_note' => 'Valid report.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reports', [
            'id' => $reportId,
            'report_status_id' => $verifiedStatusId,
            'verified_by' => $admin->id,
        ]);
    }

    public function test_user_cannot_open_admin_panel(): void
    {
        $this->seed(CityZenSeeder::class);

        $user = User::where('email', 'naufal@cityzen.test')->firstOrFail();

        $this->withSession(['cityzen_user' => CityZenAccess::sessionPayload($user)])
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_foundation_seed_prepares_admin_and_superadmin_without_places(): void
    {
        $this->seed(CityZenFoundationSeeder::class);

        $this->assertDatabaseHas('roles', ['slug' => 'admin']);
        $this->assertDatabaseHas('roles', ['slug' => 'superadmin']);
        $this->assertDatabaseHas('users', ['email' => 'admin@cityzen.test']);
        $this->assertDatabaseHas('users', ['email' => 'superadmin@cityzen.test']);
        $this->assertDatabaseCount('places', 0);

        $admin = User::where('email', 'admin@cityzen.test')->firstOrFail();
        $superadmin = User::where('email', 'superadmin@cityzen.test')->firstOrFail();

        $this->withSession(['cityzen_user' => CityZenAccess::sessionPayload($admin)])
            ->get('/admin')
            ->assertOk();

        $this->withSession(['cityzen_user' => CityZenAccess::sessionPayload($superadmin)])
            ->get('/admin/users')
            ->assertOk()
            ->assertSee('role admin/superadmin', false);
    }
}
