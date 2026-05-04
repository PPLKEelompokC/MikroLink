<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->randomElement([50000, 100000, 200000, 500000]),
            'status' => 'PENDING',
            'bank_name' => fake()->randomElement(['BCA', 'BNI', 'BRI', 'Mandiri', 'BSI']),
            'bank_account' => fake()->numerify('##########'),
            'bank_account_name' => fake()->name(),
            'proof_path' => null,
            'admin_note' => null,
        ];
    }

    /**
     * Indicate that the withdrawal is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'APPROVED',
            'proof_path' => 'withdrawals-proof/dummy.jpg',
            'admin_note' => 'Penarikan telah disetujui.',
        ]);
    }

    /**
     * Indicate that the withdrawal is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'REJECTED',
            'admin_note' => 'Saldo koperasi tidak mencukupi.',
        ]);
    }
}
