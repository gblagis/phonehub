<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Listing;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => bcrypt('password'),
        ]);


        User::create([
            'name' => 'George 2',
            'email' => 'george2@example.com',
            'password' => Hash::make('password'), 
        ]);


        Listing::factory(20)->create();
    }
}
