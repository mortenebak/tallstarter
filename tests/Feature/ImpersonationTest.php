<?php

use App\Livewire\Admin\Users;
use App\Livewire\Admin\Users\ViewUser;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;

beforeEach(function (): void {
    Permission::findOrCreate('impersonate');
});

it('shows the impersonate button on the users list for admins with the impersonate permission', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo(['view users', 'impersonate']);

    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->assertOk()
        ->assertSeeHtml(route('impersonate.store', $user))
        ->assertSee(__('users.impersonate'));
});

it('hides the impersonate button on the users list without the impersonate permission', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('view users');

    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->assertOk()
        ->assertDontSeeHtml(route('impersonate.store', $user));
});

it('hides the impersonate button on the admins own row', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo(['view users', 'impersonate']);

    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(Users::class)
        ->assertOk()
        ->assertSeeHtml(route('impersonate.store', $user))
        ->assertDontSeeHtml(route('impersonate.store', $admin));
});

it('shows the impersonate button on the user view page for admins with the impersonate permission', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo(['view users', 'impersonate']);

    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewUser::class, ['user' => $user])
        ->assertOk()
        ->assertSeeHtml(route('impersonate.store', $user));
});

it('hides the impersonate button on the user view page without the impersonate permission', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('view users');

    $user = User::factory()->create();

    Livewire::actingAs($admin)
        ->test(ViewUser::class, ['user' => $user])
        ->assertOk()
        ->assertDontSeeHtml(route('impersonate.store', $user));
});

it('hides the impersonate button on the admins own user view page', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo(['view users', 'impersonate']);

    Livewire::actingAs($admin)
        ->test(ViewUser::class, ['user' => $admin])
        ->assertOk()
        ->assertDontSeeHtml(route('impersonate.store', $admin));
});

it('lets an admin impersonate a user, see the banner and stop impersonating again', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('impersonate');

    $user = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('impersonate.store', $user))
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
    expect(session('admin_user_id'))->toBe($admin->id);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee(__('users.you_are_impersonating'))
        ->assertSee($user->name)
        ->assertSee(__('users.stop_impersonating'))
        ->assertSee(route('impersonate.destroy'));

    $this->delete(route('impersonate.destroy'))
        ->assertRedirect(route('admin.index'));

    $this->assertAuthenticatedAs($admin);
    expect(session()->has('admin_user_id'))->toBeFalse();
});

it('does not show the impersonation banner when not impersonating', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertDontSee(__('users.you_are_impersonating'));
});

it('forbids impersonation for users without the impersonate permission', function (): void {
    $user = User::factory()->create();

    $target = User::factory()->create();

    $this->actingAs($user)
        ->post(route('impersonate.store', $target))
        ->assertForbidden();

    $this->assertAuthenticatedAs($user);
    expect(session()->has('admin_user_id'))->toBeFalse();
});

it('forbids impersonation for guests', function (): void {
    $target = User::factory()->create();

    $this->post(route('impersonate.store', $target))
        ->assertRedirect(route('login'));
});

it('stores the original admin id in the session while impersonating', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('impersonate');
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('impersonate.store', $target))
        ->assertSessionHas('admin_user_id', $admin->id);
});

it('returns 404 when impersonating a non-existent user', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('impersonate');

    $this->actingAs($admin)
        ->post('/impersonate/999999')
        ->assertNotFound();
});
