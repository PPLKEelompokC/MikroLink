<?php

namespace Database\Factories;

use App\Models\FinancialRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FinancialRecord>
 */
class FinancialRecordFactory extends Factory
{
    protected $model = FinancialRecord::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'koperasi_id' => 'KOP-001',
            'record_date' => fake()->date(),
            'omzet' => fake()->randomFloat(2, 1000000, 50000000),
            'credit_score' => fake()->randomFloat(1, 5, 30),
        ];
    }
}
