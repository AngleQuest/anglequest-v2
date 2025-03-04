<?php

namespace Database\Seeders;

use App\Models\Configuration;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Configuration::create([
            'usd_rate' => 750,
            'email_verify' => 'enabled',
        ]);
    }
}
