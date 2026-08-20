<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => env('GMARK_SEED_ADMIN_EMAIL', 'admin@gmark.com')],
            ['name' => 'GMark Administrator', 'password' => Hash::make(env('GMARK_SEED_ADMIN_PASSWORD', 'password')), 'is_active' => true, 'is_admin' => true, 'email_verified_at' => now()],
        );
    }
}
