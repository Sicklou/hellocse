<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Administrateur;
use App\Models\User;

uses(RefreshDatabase::class);

test('user is an admin', function () {
    $user = User::factory()
        ->create();

    Administrateur::factory()
        ->for($user)
        ->create();

    expect($user->isAdmin())->toBeTrue();
});

test('user is not an admin', function () {
    $user = User::factory()
        ->create();

    expect($user->isAdmin())->toBeFalse();
});
