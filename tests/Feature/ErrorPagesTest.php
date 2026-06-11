<?php

use App\Models\User;

test('a missing page renders the custom 404 error page', function (): void {
    $response = $this->get('/this-page-does-not-exist');

    $response->assertNotFound();
    $response->assertSee(__('errors.404_title'));
    $response->assertSee(__('errors.404_message'));
    $response->assertSee(__('errors.back_to_home'));
    $response->assertSee(route('home'));
});

test('an unauthorized user gets the custom 403 error page on admin routes', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/admin');

    $response->assertForbidden();
    $response->assertSee(__('errors.403_title'));
    $response->assertSee(__('errors.403_message'));
    $response->assertSee(__('errors.back_to_home'));
});

test('guests hitting admin routes are redirected to login', function (): void {
    $response = $this->get('/admin');

    $response->assertRedirect(route('login'));
});

test('the custom 404 error page is translated in danish', function (): void {
    app()->setLocale('da');

    $response = $this->get('/this-page-does-not-exist');

    $response->assertNotFound();
    $response->assertSee('Siden blev ikke fundet');
});
