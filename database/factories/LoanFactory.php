<?php

namespace Database\Factories;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    protected $model = Loan::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $types = ['Pinjaman Usaha', 'Pinjaman Konsumsi', 'Pinjaman Darurat'];

        return [
            'user_id'             => User::factory(),
            'type'                => fake()->randomElement($types),
            'amount'              => fake()->randomElement([5_000_000, 10_000_000, 15_000_000, 20_000_000]),
            'duration'            => fake()->randomElement([6, 12, 24, 36]),
            'reason'              => fake()->sentence(12),
            'status'              => 'Baru',
            'progress_percentage' => 0,
            'notes'               => null,
            'disbursed_at'        => null,
        ];
    }
}
