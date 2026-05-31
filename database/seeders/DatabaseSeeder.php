<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        // $this->call(ProjectSeeder::class);
        // $this->call(ProjectProposalSeeder::class);
        // $this->call(ProjectCharterSeeder::class);

    }
}
