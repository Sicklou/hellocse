<?php

use App\Models\Enum\ProfilStatut;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Profil;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('it can retrieve list of active profils', function () {

    Profil::factory(5)->create([
        'statut' => ProfilStatut::Actif->value,
    ]);

    $response = $this->get('/api/profils');
    $response->assertStatus(200);

    expect($response->getContent())->toBeJson();
    expect($response->json())->toHaveCount(5);
});

test('authenticated user can see statut field', function () {
    Sanctum::actingAs(
        User::factory()->create()
    );

    Profil::factory()->create([
        'statut' => ProfilStatut::Actif->value,
    ]);

    $response = $this->get('/api/profils');
    $response->assertStatus(200);

    $jsonResponse = $response->json();
    $firstProfile = $jsonResponse[0];

    expect(array_key_exists('statut', $firstProfile))->toBeTrue();
});

test("unauthenticated user can't see statut field", function () {
    Profil::factory()->create([
        'statut' => ProfilStatut::Actif->value,
    ]);

    $response = $this->get('/api/profils');
    $response->assertStatus(200);

    $jsonResponse = $response->json();
    $firstProfile = $jsonResponse[0];

    expect(array_key_exists('statut', $firstProfile))->toBeFalse();
});
