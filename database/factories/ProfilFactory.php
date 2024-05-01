<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profil>
 */
class ProfilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->name,
            'prenom' => $this->faker->firstName,
            'image' => $this->faker->filePath(),
            'statut' => $this->faker->randomElement(['inactif', 'en attente', 'actif']),
        ];
    }

    /**
     * Indicate that the user is suspended.
     */
    public function withGeneratedImage(): Factory
    {
        return $this->state(function (array $attributes) {
            $filePath = storage_path('app/images/profils');
            return [
                'image' => basename($this->faker->image(dir: $filePath, width: 80, height: 80))
            ];
        });
    }

}
