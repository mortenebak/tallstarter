<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! Plan::query()->where('slug', '=', 'pro-monthly')->exists()) {
            Plan::query()->create([
                'title' => 'Pro - 9 USD / month',
                'slug' => 'pro-monthly',
                'stripe_id' => 'price_',
            ]);
        }
        if (! Plan::query()->where('slug', '=', 'pro-yearly')->exists()) {
            Plan::query()->create([
                'title' => 'Pro - 99 USD / year',
                'slug' => 'pro-yearly',
                'stripe_id' => 'price_',
            ]);
        }
    }
}
