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

    $response = $this->post('/api/profils', [
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
