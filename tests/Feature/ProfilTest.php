<?php

use App\Models\Administrateur;
use App\Models\Enum\ProfilStatut;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Profil;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

test('everyone can retrieve list of active profils', function () {
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

test('everyone can retrieve an active profil', function () {
    $this->withoutExceptionHandling();
    $profil = Profil::factory()->create([
        'statut' => ProfilStatut::Actif->value,
    ]);

    $response = $this->get("/api/profils/{$profil->id}");
    $response->assertStatus(200);

    expect($response->getContent())->toBeJson();
    expect($response->getContent())
        ->json()
        ->toHaveCount(6)
        ->nom->toBe($profil->nom)
        ->prenom->toBe($profil->prenom);
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

test('admin can see `statut` field of profil', function () {
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

test('unauthenticated user cant access private endpoints', function() {
    $response = $this->postJson('/api/profils');
    $response->assertStatus(401);

    $response = $this->deleteJson('/api/profils/1');
    $response->assertStatus(401);
});

test('admin can delete profil', function() {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    Administrateur::factory()
        ->for($user)
        ->create();
    Sanctum::actingAs($user);

    $profil = Profil::factory()->create([
        'statut' => ProfilStatut::Actif->value,
    ]);

    $this->assertDatabaseCount('profils', 1);
    $this->assertModelExists($profil);

    $response = $this->deleteJson("/api/profils/1");
    $response->assertStatus(200);

    $this->assertModelMissing($profil);
    $this->assertDatabaseCount('profils', 0);
});
