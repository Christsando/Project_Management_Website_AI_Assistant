<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'TeamIT',
            'email' => 'teamit@psm.com',
            'password' => Hash::make('teamit123'),
            'role' => 'IT'
        ]);

        User::create([
            'name' => 'PMO',
            'email' => 'pmo@psm.com',
            'password' => Hash::make('pmo123'),
            'role' => 'Project Management Officer',
        ]);
    }
}
