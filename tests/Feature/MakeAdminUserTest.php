<?php

use App\Models\User;

test('grants admin access to user by email', function (): void {
    $user = User::factory()->create(['email' => 'admin@example.com', 'is_admin' => false]);

    $this->artisan('orewire:make-admin', ['email' => 'admin@example.com'])
        ->assertSuccessful();

    $user->refresh();
    expect($user->is_admin)->toBeTrue();
});

test('reports success when user already has admin access', function (): void {
    User::factory()->create(['email' => 'admin@example.com', 'is_admin' => true]);

    $this->artisan('orewire:make-admin', ['email' => 'admin@example.com'])
        ->assertSuccessful();
});

test('fails when user does not exist', function (): void {
    $this->artisan('orewire:make-admin', ['email' => 'nobody@example.com'])
        ->assertFailed();
});
