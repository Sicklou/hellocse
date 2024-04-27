<?php

namespace Database\Seeders;

use App\Models\Administrateur;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create(['name' => 'admin',
            'email' => 'test@example.com',
            'password' => bcrypt('test1234'),
        ]);

        Administrateur::create(['user_id' => $user->id]);
    }
}
