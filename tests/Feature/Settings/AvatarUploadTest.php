<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('avatar can be uploaded and is stored on the user', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->set('avatar', UploadedFile::fake()->image('avatar.png'));

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->avatar)->not->toBeNull();
    expect($user->avatar)->toStartWith('avatars/');
    Storage::disk('public')->assertExists($user->avatar);
});

test('replacing the avatar deletes the old file', function (): void {
    Storage::fake('public');

    $oldPath = UploadedFile::fake()->image('old.png')->store('avatars', 'public');

    $user = User::factory()->create(['avatar' => $oldPath]);

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->set('avatar', UploadedFile::fake()->image('new.png'));

    $response->assertHasNoErrors();

    $user->refresh();

    expect($user->avatar)->not->toEqual($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($user->avatar);
});

test('avatar can be removed', function (): void {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('avatar.png')->store('avatars', 'public');

    $user = User::factory()->create(['avatar' => $path]);

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->call('removeAvatar');

    $response->assertHasNoErrors();

    expect($user->refresh()->avatar)->toBeNull();
    Storage::disk('public')->assertMissing($path);
});

test('a non-image file is rejected', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->set('avatar', UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'));

    $response->assertHasErrors(['avatar']);

    expect($user->refresh()->avatar)->toBeNull();
});

test('an image larger than 2MB is rejected', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.profile')
        ->set('avatar', UploadedFile::fake()->image('huge.png')->size(3000));

    $response->assertHasErrors(['avatar' => 'max']);

    expect($user->refresh()->avatar)->toBeNull();
});

test('avatar url returns the storage url when an avatar is set', function (): void {
    Storage::fake('public');

    $user = User::factory()->create(['avatar' => 'avatars/avatar.png']);

    expect($user->avatarUrl())->toEqual(Storage::disk('public')->url('avatars/avatar.png'));
});

test('avatar url returns null when no avatar is set', function (): void {
    $user = User::factory()->create();

    expect($user->avatarUrl())->toBeNull();
});

test('avatar file is deleted when the account is deleted', function (): void {
    Storage::fake('public');

    $path = UploadedFile::fake()->image('avatar.png')->store('avatars', 'public');

    $user = User::factory()->create(['avatar' => $path]);

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response->assertHasNoErrors();

    expect($user->fresh())->toBeNull();
    Storage::disk('public')->assertMissing($path);
});
