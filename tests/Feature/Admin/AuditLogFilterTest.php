<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Writing;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    foreach (['super-admin', 'admin', 'editor', 'viewer'] as $role) {
        Role::findOrCreate($role, 'web');
    }

    config(['security.require_2fa_for_admin' => false]);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('super-admin');
});

function makeLog(array $attrs = []): Activity
{
    /** @var Activity $log */
    $log = Activity::create(array_merge([
        'log_name' => 'default',
        'description' => 'test',
        'event' => 'updated',
    ], $attrs));

    // Override timestamps if provided since Eloquent ignores them on create.
    if (isset($attrs['created_at'])) {
        $log->created_at = $attrs['created_at'];
        $log->save();
    }

    return $log;
}

it('filters audit log by event name', function () {
    makeLog(['log_name' => 'auth', 'event' => 'login.success', 'description' => 'login ok']);
    makeLog(['log_name' => 'default', 'event' => 'created', 'description' => 'writing created']);

    $response = $this->actingAs($this->admin)->get('/admin/audit-log?event=login.success');

    $response->assertOk();
    $response->assertSee('login ok');
    $response->assertDontSee('writing created');
});

it('filters audit log by causer id', function () {
    $alice = User::factory()->create(['name' => 'Alice Causer']);
    $bob = User::factory()->create(['name' => 'Bob Causer']);

    makeLog(['causer_type' => User::class, 'causer_id' => $alice->id, 'description' => 'alice did it']);
    makeLog(['causer_type' => User::class, 'causer_id' => $bob->id, 'description' => 'bob did it']);

    $response = $this->actingAs($this->admin)->get('/admin/audit-log?causer='.$alice->id);

    $response->assertOk();
    $response->assertSee('alice did it');
    $response->assertDontSee('bob did it');
});

it('filters audit log by subject_type', function () {
    $writing = Writing::factory()->create();

    makeLog([
        'subject_type' => Writing::class,
        'subject_id' => $writing->id,
        'event' => 'created',
        'description' => 'writing subject here',
    ]);
    makeLog([
        'subject_type' => User::class,
        'subject_id' => $this->admin->id,
        'event' => 'updated',
        'description' => 'user subject here',
    ]);

    $subject = urlencode(Writing::class);
    $response = $this->actingAs($this->admin)->get('/admin/audit-log?subject='.$subject);

    $response->assertOk();
    $response->assertSee('writing subject here');
    $response->assertDontSee('user subject here');
});

it('filters audit log by date range', function () {
    makeLog(['description' => 'old entry', 'created_at' => now()->subDays(10)]);
    makeLog(['description' => 'recent entry', 'created_at' => now()->subDay()]);

    $from = now()->subDays(3)->toDateString();
    $to = now()->toDateString();

    $response = $this->actingAs($this->admin)->get("/admin/audit-log?from={$from}&to={$to}");

    $response->assertOk();
    $response->assertSee('recent entry');
    $response->assertDontSee('old entry');
});

it('applies combined filters (event + causer + date)', function () {
    $alice = User::factory()->create(['name' => 'Alice Combo']);
    $bob = User::factory()->create(['name' => 'Bob Combo']);

    // Matches all three filters
    makeLog([
        'event' => 'created',
        'causer_type' => User::class,
        'causer_id' => $alice->id,
        'description' => 'match combined',
        'created_at' => now()->subHour(),
    ]);

    // Wrong event
    makeLog([
        'event' => 'deleted',
        'causer_type' => User::class,
        'causer_id' => $alice->id,
        'description' => 'wrong event',
        'created_at' => now()->subHour(),
    ]);

    // Wrong causer
    makeLog([
        'event' => 'created',
        'causer_type' => User::class,
        'causer_id' => $bob->id,
        'description' => 'wrong causer',
        'created_at' => now()->subHour(),
    ]);

    // Outside date range
    makeLog([
        'event' => 'created',
        'causer_type' => User::class,
        'causer_id' => $alice->id,
        'description' => 'too old',
        'created_at' => now()->subDays(15),
    ]);

    $from = now()->subDays(3)->toDateString();
    $response = $this->actingAs($this->admin)
        ->get("/admin/audit-log?event=created&causer={$alice->id}&from={$from}");

    $response->assertOk();
    $response->assertSee('match combined');
    $response->assertDontSee('wrong event');
    $response->assertDontSee('wrong causer');
    $response->assertDontSee('too old');
});

it('preserves filters across pagination links', function () {
    for ($i = 0; $i < 35; $i++) {
        makeLog(['event' => 'created', 'description' => "entry {$i}"]);
    }

    $response = $this->actingAs($this->admin)->get('/admin/audit-log?event=created');
    $response->assertOk();
    // withQueryString on paginator preserves event=created in link hrefs
    $response->assertSee('event=created', false);
});
