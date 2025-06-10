<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;
use App\Models\Aid;

class ConditionSeeder extends Seeder
{
    public function run()
    {
        $aids = Aid::all();
        foreach ($aids as $aid) {
            Condition::factory()->count(2)->create([
                'aid_id' => $aid->id,
            ]);
        }
    }
}