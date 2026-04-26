<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->with('roles:id,name')
            ->orderBy('name');

        $role = trim((string) $request->string('role')->value());
        if ($role !== '' && in_array($role, UserRequest::ASSIGNABLE_ROLES, true)) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        $search = trim((string) $request->string('q')->value());
        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => UserRequest::ASSIGNABLE_ROLES,
            'filters' => [
                'role' => $role,
                'q' => $search,
            ],
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', User::class);

        $user = new User;

        return view('admin.users.create', [
            'user' => $user,
            'currentRole' => '',
            'availableRoles' => $this->availableRolesFor($request->user()),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->authorize('create', User::class);

        $data = $request->validated();
        $role = $this->guardRoleChoice($request, $data['role']);

        $user = new User;
        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'password_changed_at' => now(),
            'locale' => 'tr',
        ])->save();

        $user->syncRoles([$role]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('Kullanıcı oluşturuldu.'));
    }

    public function edit(Request $request, User $user): View
    {
        $this->authorize('update', $user);

        return view('admin.users.edit', [
            'user' => $user,
            'currentRole' => (string) $user->roles->pluck('name')->first(),
            'availableRoles' => $this->availableRolesFor($request->user()),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $this->authorize('update', $user);

        $data = $request->validated();
        $role = $this->guardRoleChoice($request, $data['role']);

        $user->forceFill([
            'name' => $data['name'],
            'email' => $data['email'],
        ])->save();

        if (! empty($data['password'])) {
            $user->forceFill([
                'password' => bcrypt($data['password']),
                'password_changed_at' => now(),
            ])->save();
        }

        $user->syncRoles([$role]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('Kullanıcı güncellendi.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('Kullanıcı silindi.'));
    }

    /**
     * Roles that the given actor is allowed to assign via the UI.
     * Only existing super-admins may hand out the super-admin role.
     *
     * @return array<int, string>
     */
    private function availableRolesFor(User $actor): array
    {
        if ($actor->hasRole('super-admin')) {
            return UserRequest::ASSIGNABLE_ROLES;
        }

        return array_values(array_filter(
            UserRequest::ASSIGNABLE_ROLES,
            fn (string $name): bool => $name !== 'super-admin',
        ));
    }

    /**
     * Block a non-super-admin from escalating anyone to super-admin via crafted POST.
     * Falls back to a safe default if the actor tries it.
     */
    private function guardRoleChoice(Request $request, string $requested): string
    {
        if ($requested === 'super-admin' && ! $request->user()->hasRole('super-admin')) {
            abort(403, 'super-admin rolü yalnızca mevcut bir super-admin tarafından atanabilir.');
        }

        // Ensure the role row exists (seeded in RoleSeeder, but be defensive).
        Role::findOrCreate($requested, 'web');

        return $requested;
    }
}
