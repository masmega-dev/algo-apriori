<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'),
            ],
        )->forceFill([
            'email_verified_at' => now(),
        ])->save();

        $this->call(CakeShopSeeder::class);

        if (app()->environment(['local', 'testing'])) {
            $this->call(AprioriDemoSeeder::class);
        }
    }
}
