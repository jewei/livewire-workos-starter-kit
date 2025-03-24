<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'email' => 'jewei@duck.com',
            'workos_id' => '0000000000',
            'first_name' => 'Admin',
            'last_name' => '',
            'referral_code' => 'GGWP',
            'locale' => 'en',
            'timezone' => 'Asia/Kuala_Lumpur',
            'source' => 'Seeder',
        ]);
    }
}
