<?php

use App\Models\Administrateur;
use App\Models\Profil;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $user = User::factory()->create();
    Administrateur::factory()
        ->for($user)
        ->create();

    Sanctum::actingAs($user);
});

test('admin can create profil', function () {
    Storage::fake('local');
    $profil = Profil::factory()->make();
    $file = UploadedFile::fake()->image('test.png');

    $response = $this->postJson('/api/profils', [
        'nom' => $profil->nom,
        'prenom' => $profil->prenom,
        'image' => $file,
        'statut' => $profil->statut->value,
    ]);

    $response->assertStatus(200);
    Storage::disk('local')->assertExists($response->json('image'));
    $this->assertDatabaseHas('profils', [
        'nom' => $profil->nom,
        'prenom' => $profil->prenom,
        'statut' => $profil->statut->value
    ]);
});

test('`nom` field is required', function () {
    Storage::fake('local');
    $profil = Profil::factory()->make();
    $file = UploadedFile::fake()->image('test.png');

    $response = $this->postJson('/api/profils', [
        'nom' => null,
        'prenom' => $profil->prenom,
        'image' => $file,
        'statut' => $profil->statut->value,
    ]);

    $response->assertStatus(422);

    expect($response->json())->toHaveKeys([
        'message',
        'errors.nom'
    ]);
});

test('`prenom` field is required', function () {
    Storage::fake('local');
    $profil = Profil::factory()->make();
    $file = UploadedFile::fake()->image('test.png');

    $response = $this->postJson('/api/profils', [
        'nom' => $profil->nom,
        'prenom' => null,
        'image' => $file,
        'statut' => $profil->statut->value,
    ]);

    $response->assertStatus(422);

    expect($response->json())->toHaveKeys([
        'message',
        'errors.prenom'
    ]);
});

test('`image` field is required and type is image', function () {
    $profil = Profil::factory()->make();

    Storage::fake('local');
    $file = UploadedFile::fake()->image('test.txt');

    $response = $this->postJson('/api/profils', [
        'nom' => $profil->nom,
        'prenom' => $profil->prenom,
        'image' => $file,
        'statut' => $profil->statut->value,
    ]);

    $response->assertStatus(422);

    expect($response->json())->toHaveKeys([
        'message',
        'errors.image'
    ]);

    $response = $this->postJson('/api/profils', [
        'nom' => $profil->nom,
        'prenom' => $profil->prenom,
        'image' => null,
        'statut' => $profil->statut->value,
    ]);

    $response->assertStatus(422);

    expect($response->json())->toHaveKeys([
        'message',
        'errors.image'
    ]);
});

test('`statut` field is required and value is one of ProfilStatut', function () {
    $profil = Profil::factory()->make();

    Storage::fake('local');
    $file = UploadedFile::fake()->image('test.png');

    $response = $this->postJson('/api/profils', [
        'nom' => $profil->nom,
        'prenom' => $profil->prenom,
        'image' => $file,
        'statut' => null,
    ]);

    $response->assertStatus(422);

    expect($response->json())->toHaveKeys([
        'message',
        'errors.statut'
    ]);

    $response = $this->postJson('/api/profils', [
        'nom' => $profil->nom,
        'prenom' => $profil->prenom,
        'image' => $file,
        'statut' => "test",
    ]);

    $response->assertStatus(422);

    expect($response->json())->toHaveKeys([
        'message',
        'errors.statut'
    ]);
});
