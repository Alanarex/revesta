<?php

namespace Database\Seeders;

use App\Models\Ad;
use App\Models\AdImage;
use Illuminate\Database\Seeder;

class AdImageSeeder extends Seeder
{
    public function run(): void
    {
        $ads = Ad::all();

        if ($ads->isEmpty()) {
            $this->command->warn('No ads found. Run AdSeeder first.');

            return;
        }

        $this->command->info('Creating images for ads...');

        $total = 0;
        foreach ($ads as $ad) {
            $count = rand(2, 5);
            AdImage::factory()->count($count)->create(['ad_id' => $ad->id]);
            $total += $count;
        }

        $this->command->info("✅ Created {$total} ad images.");
    }
}
