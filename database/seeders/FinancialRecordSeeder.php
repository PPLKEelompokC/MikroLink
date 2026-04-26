<?php

namespace Database\Seeders;

use App\Models\FinancialRecord;
use App\Models\Koperasi;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinancialRecordSeeder extends Seeder
{
    /**
     * Seed the financial records with realistic trending data.
     *
     * Omzet trends upward over 15 months (Jul Y1 → Sep Y2).
     * Credit score stays relatively low/stable (derived from existing data patterns).
     */
    public function run(): void
    {
        $koperasi = Koperasi::firstOrCreate(
            ['id_koperasi' => 'KOP-001'],
            ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
        );

        $startDate = Carbon::now()->subMonths(14)->startOfMonth();

        // Base omzet values that trend upward with some natural variance
        $omzetTrend = [
            5000000,   // Jul  - starting low
            5500000,   // Aug
            7000000,   // Sep  - slight bump
            8500000,   // Oct
            12000000,  // Nov  - growth
            14000000,  // Dec
            15500000,  // Jan  - steady climb
            18000000,  // Feb
            20000000,  // Mar
            22000000,  // Apr
            25000000,  // May
            30000000,  // Jun  - acceleration
            35000000,  // Jul
            42000000,  // Aug  - strong growth
            55000000,  // Sep  - peak
        ];

        // Credit score derived from existing capital log patterns (stays low, slow improvement)
        $creditScoreTrend = [
            3.0, 3.5, 4.0, 4.2, 5.0, 5.5, 6.0, 6.5, 7.0,
            7.5, 8.0, 9.0, 10.0, 10.5, 11.0,
        ];

        foreach ($omzetTrend as $index => $baseOmzet) {
            $recordDate = $startDate->copy()->addMonths($index);

            // Add some daily variance within each month
            $daysInMonth = $recordDate->daysInMonth;
            $dailyOmzet = $baseOmzet / $daysInMonth;

            // Create one aggregated record per month
            FinancialRecord::updateOrCreate(
                [
                    'koperasi_id' => $koperasi->id_koperasi,
                    'record_date' => $recordDate->format('Y-m-d'),
                ],
                [
                    'omzet' => $baseOmzet + rand(-500000, 500000),
                    'credit_score' => $creditScoreTrend[$index] + (rand(-5, 5) / 10),
                ]
            );
        }
    }
}
