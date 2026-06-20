<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Challenge;
use App\Models\Record;
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
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@versusfit.local',
            'password' => bcrypt('password'),
        ]);

        $users = User::factory()->count(4)->create();

        $challenges = Challenge::factory()->count(5)->create();

        foreach ($challenges as $challenge) {
            $challenge->members()->attach($users->random(3)->pluck('id'));
        }

        Record::factory()->count(30)->create([
            'challenge_id' => fn() => $challenges->random()->id,
            'user_id' => fn() => $users->random()->id,
        ]);
    }
}
