<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->warn('No users found. Run UserSeeder first.');

            return;
        }

        $this->command->info('Creating notifications...');

        $total = 0;
        foreach ($users as $user) {
            $count = rand(1, 4);
            Notification::factory()->count($count)->create(['user_id' => $user->id]);
            $total += $count;
        }

        $this->command->info("✅ Created {$total} notifications.");
    }
}
