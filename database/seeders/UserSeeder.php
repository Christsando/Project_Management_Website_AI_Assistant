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
        User::updateOrCreate(
            ['email' => 'teamit@psm.com'],
            [
                'name' => 'TeamIT',
                'password' => Hash::make('teamit123'),
                'role' => 'IT'
            ]
        );

        User::updateOrCreate(
            ['email' => 'pmo@psm.com'],
            [
                'name' => 'PMO',
                'password' => Hash::make('pmo123'),
                'role' => 'Project Management Officer',
            ]
        );

        // Project Manager
        User::updateOrCreate(
            ['email' => 'pm@psm.com'],
            [
                'name' => 'Project Manager',
                'password' => Hash::make('pm123'),
                'role' => 'Project Manager',
            ]
        );

        // Manager
        User::updateOrCreate(
            ['email' => 'manager@psm.com'],
            [
                'name' => 'Manager',
                'password' => Hash::make('manager123'),
                'role' => 'Manager',
            ]
        );
    }
}
