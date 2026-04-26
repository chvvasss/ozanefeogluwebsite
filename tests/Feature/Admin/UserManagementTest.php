<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\UserController;
use App\Models\User;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;

/*
 * Notes on this test bootstrap:
 *
 * - The policy binding (Gate::policy(User::class, UserPolicy::class)) and the
 *   route definitions live outside this test (AppServiceProvider + routes/web.php).
 *   In case the delivery merges in stages (policy/routes added by the boss),
 *   we register both here defensively so the tests pass today and stay green
 *   after the boss wires the globals. Double-registration is a no-op.
 */

beforeEach(function (): void {
    foreach (['super-admin', 'admin', 'editor', 'contributor', 'viewer'] as $role) {
        Role::findOrCreate($role, 'web');
    }

    config(['security.require_2fa_for_admin' => false]);

    Gate::policy(User::class, UserPolicy::class);

    if (! Route::has('admin.users.index')) {
        Route::middleware(['web', 'auth', 'ensure.2fa'])
            ->prefix('admin')
            ->as('admin.')
            ->group(function (): void {
                Route::resource('users', UserController::class);
            });
    }
});

it('renders the users index for an admin', function () {
    $admin = User::factory()->create(['name' => 'Ada Admin']);
    $admin->assignRole('admin');

    $other = User::factory()->create(['name' => 'Bora Editor', 'email' => 'bora@example.com']);
    $other->assignRole('editor');

    $response = $this->actingAs($admin)->get('/admin/users');

    $response->assertOk();
    $response->assertSee('Ada Admin');
    $response->assertSee('bora@example.com');
});

it('creates a new user with a role assigned', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post('/admin/users', [
        'name' => 'Cem Katkici',
        'email' => 'cem@example.com',
        'password' => 'Super-Strong-Passw0rd',
        'password_confirmation' => 'Super-Strong-Passw0rd',
        'role' => 'contributor',
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHasNoErrors();

    $fresh = User::query()->where('email', 'cem@example.com')->first();
    expect($fresh)->not->toBeNull();
    expect($fresh->name)->toBe('Cem Katkici');
    expect($fresh->hasRole('contributor'))->toBeTrue();
    expect($fresh->password_changed_at)->not->toBeNull();
});

it('updates an existing user without changing the password when blank', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $target = User::factory()->create(['name' => 'Eski İsim']);
    $target->assignRole('editor');
    $originalHash = $target->password;

    $response = $this->actingAs($admin)->put("/admin/users/{$target->id}", [
        'name' => 'Yeni İsim',
        'email' => $target->email,
        'password' => '',
        'password_confirmation' => '',
        'role' => 'viewer',
    ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHasNoErrors();

    $target->refresh();
    expect($target->name)->toBe('Yeni İsim');
    expect($target->password)->toBe($originalHash);
    expect($target->hasRole('viewer'))->toBeTrue();
    expect($target->hasRole('editor'))->toBeFalse();
});

it('soft-deletes a user via destroy', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $target = User::factory()->create();
    $target->assignRole('viewer');

    $this->actingAs($admin)
        ->delete("/admin/users/{$target->id}")
        ->assertRedirect(route('admin.users.index'));

    expect(User::query()->find($target->id))->toBeNull();
    expect(User::withTrashed()->find($target->id))->not->toBeNull();
});

it('denies user management for contributors', function () {
    $contributor = User::factory()->create();
    $contributor->assignRole('contributor');

    $target = User::factory()->create();
    $target->assignRole('viewer');

    $this->actingAs($contributor)
        ->get('/admin/users/create')
        ->assertForbidden();

    $this->actingAs($contributor)
        ->post('/admin/users', [
            'name' => 'X',
            'email' => 'x@example.com',
            'password' => 'Super-Strong-Passw0rd',
            'password_confirmation' => 'Super-Strong-Passw0rd',
            'role' => 'viewer',
        ])
        ->assertForbidden();

    $this->actingAs($contributor)
        ->delete("/admin/users/{$target->id}")
        ->assertForbidden();
});

it('forbids an admin from editing or deleting a super-admin', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $superAdmin = User::factory()->create(['name' => 'Kaptan']);
    $superAdmin->assignRole('super-admin');

    $this->actingAs($admin)
        ->get("/admin/users/{$superAdmin->id}/edit")
        ->assertForbidden();

    $this->actingAs($admin)
        ->put("/admin/users/{$superAdmin->id}", [
            'name' => 'Düşürülmüş',
            'email' => $superAdmin->email,
            'password' => '',
            'password_confirmation' => '',
            'role' => 'viewer',
        ])
        ->assertForbidden();

    $this->actingAs($admin)
        ->delete("/admin/users/{$superAdmin->id}")
        ->assertForbidden();

    expect(User::find($superAdmin->id))->not->toBeNull();
});

it('prevents an admin from assigning the super-admin role', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)
        ->post('/admin/users', [
            'name' => 'Escalator',
            'email' => 'escalate@example.com',
            'password' => 'Super-Strong-Passw0rd',
            'password_confirmation' => 'Super-Strong-Passw0rd',
            'role' => 'super-admin',
        ])
        ->assertForbidden();

    expect(User::query()->where('email', 'escalate@example.com')->exists())->toBeFalse();
});
