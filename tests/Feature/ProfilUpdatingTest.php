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

    $this->fileOrigin = UploadedFile::fake()->image('test.png');
    $this->profilOrigin = Profil::factory()->create([
        'image' => $this->fileOrigin->hashName("images/profils/"),
    ]);

});

test('admin can update profil', function () {
    $this->withoutExceptionHandling();
    Storage::fake('local');


    $this->assertDatabaseHas('profils', [
        'nom' => $this->profilOrigin->nom,
        'prenom' => $this->profilOrigin->prenom,
        'statut' => $this->profilOrigin->statut->value,
        'image' => $this->profilOrigin->image
    ]);

    $fileUpdated = UploadedFile::fake()->image('testUpdated.png');
    $profilUpdated = Profil::factory()->make([
        'image' => $fileUpdated->hashName("images/profils/"),
    ]);

    $response = $this->putJson("/api/profils/{$this->profilOrigin->id}", [
        'nom' => $profilUpdated->nom,
        'prenom' => $profilUpdated->prenom,
        'image' => $fileUpdated,
        'statut' => $profilUpdated->statut->value,
    ]);

    // Retourne bien le profil
    $response
        ->assertStatus(200)
        ->assertJson([
            'message' => 'updated',
            'data' => $profilUpdated->toArray(),
        ]);

    // Fichier bien stocké
    Storage::disk('local')->assertExists($response->json('image'));
    // Enregistrement dans la base
    $this->assertDatabaseHas('profils', [
        'nom' => $profilUpdated->nom,
        'prenom' => $profilUpdated->prenom,
        'statut' => $profilUpdated->statut->value,
        'image' => $profilUpdated->image
    ]);

});


test('`nom` field is required', function () {
    Storage::fake('local');
    $profil = Profil::factory()->make();
    $file = UploadedFile::fake()->image('test.png');

    $response = $this->putJson("/api/profils/{$this->profilOrigin->id}", [
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

    $response = $this->putJson("/api/profils/{$this->profilOrigin->id}", [
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

    $response = $this->putJson("/api/profils/{$this->profilOrigin->id}", [
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

    $response = $this->putJson("/api/profils/{$this->profilOrigin->id}", [
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

    $response = $this->putJson("/api/profils/{$this->profilOrigin->id}", [
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

    $response = $this->putJson("/api/profils/{$this->profilOrigin->id}", [
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
