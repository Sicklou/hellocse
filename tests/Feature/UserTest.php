<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Administrateur;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

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

test('user can authenticate', function() {

    $user = User::factory()
        ->create(['password' => Hash::make('password')]);

    Administrateur::factory()
        ->for($user)
        ->create();

    $response = $this->postJson('/api/token/create',
        [
            'email' => $user->email,
            'password' => 'password',
        ]
    );

    $response->assertOk();
    expect($response->getContent())->tobeJson();

    // Test token valide
    $data = $response->json();
    $persistedToken = PersonalAccessToken::findToken($data['token'])->first();
    $userToken = $persistedToken->tokenable;
    expect($user->id)->toBe($userToken->id);
});

test('user cant authenticate', function() {

    $user = User::factory()
        ->create(['password' => Hash::make('password')]);

    Administrateur::factory()
        ->for($user)
        ->create();

    // wrong password
    $response = $this->postJson('/api/token/create',
        [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]
    );

    $response->assertStatus(401);

    //wrong user
    $response = $this->postJson('/api/token/create',
        [
            'email' => 'test@test.com',
            'password' => 'password',
        ]
    );

    $response->assertStatus(401);
});

test('user cant authenticate if not admin', function() {

    $user = User::factory()
        ->create(['password' => Hash::make('password')]);

    // is not admin
    $response = $this->postJson('/api/token/create',
        [
            'email' => $user->email,
            'password' => 'password',
        ]
    );

    $response->assertStatus(403);
});
