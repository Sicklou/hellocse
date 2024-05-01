<?php

use App\Models\Administrateur;
use App\Models\Enum\ProfilStatut;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Profil;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('anyone can retrieve list of active profils', function () {
    Profil::factory(5)->create([
        'statut' => ProfilStatut::Actif->value,
    ]);

    Profil::factory(1)->create([
        'statut' => ProfilStatut::Inactif->value,
    ]);

    $response = $this->get('/api/profils');
    $response->assertStatus(200);

    expect($response->getContent())->toBeJson();
    expect($response->json())->toHaveCount(5);
});

test("anyone can't see statut field of profil", function () {
    Profil::factory()->create([
        'statut' => ProfilStatut::Actif->value,
    ]);

    $response = $this->get('/api/profils');
    $response->assertStatus(200);

    $jsonResponse = $response->json();
    $firstProfile = $jsonResponse[0];

    expect(array_key_exists('statut', $firstProfile))->toBeFalse();
});

test('authenticated user can see `statut` field of profil', function () {
    $user = User::factory()->create();
    Administrateur::factory()
        ->for($user)
        ->create();
    Sanctum::actingAs($user);

    Profil::factory()->create([
        'statut' => ProfilStatut::Actif->value,
    ]);

    $response = $this->get('/api/profils');
    $response->assertStatus(200);

    $jsonResponse = $response->json();
    $firstProfile = $jsonResponse[0];

    expect(array_key_exists('statut', $firstProfile))->toBeTrue();
});
