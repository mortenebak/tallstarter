<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Permission::create(['name' => 'impersonate']);
});

it('allows a user with the impersonate permission to impersonate another user', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('impersonate');
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('impersonate.store', $target))
        ->assertRedirect(route('dashboard'));

    expect(auth()->id())->toBe($target->id);
});

it('stores the original admin id in the session while impersonating', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('impersonate');
    $target = User::factory()->create();

    $this->actingAs($admin)
        ->post(route('impersonate.store', $target))
        ->assertSessionHas('admin_user_id', $admin->id);
});

it('forbids a user without the impersonate permission', function (): void {
    $user = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($user)
        ->post(route('impersonate.store', $target))
        ->assertForbidden();

    expect(auth()->id())->toBe($user->id);
    expect(session('admin_user_id'))->toBeNull();
});

it('redirects guests to the login page', function (): void {
    $target = User::factory()->create();

    $this->post(route('impersonate.store', $target))
        ->assertRedirect(route('login'));
});

it('allows the admin to stop impersonating and return to their own account', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('impersonate');
    $target = User::factory()->create();

    $this->actingAs($admin)->post(route('impersonate.store', $target));

    expect(auth()->id())->toBe($target->id);

    $this->delete(route('impersonate.destroy'))
        ->assertRedirect(route('admin.index'));

    expect(auth()->id())->toBe($admin->id);
    expect(session('admin_user_id'))->toBeNull();
});

it('returns 404 when impersonating a non-existent user', function (): void {
    $admin = User::factory()->create();
    $admin->givePermissionTo('impersonate');

    $this->actingAs($admin)
        ->post('/impersonate/999999')
        ->assertNotFound();
});
