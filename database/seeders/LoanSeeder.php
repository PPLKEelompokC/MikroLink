<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\LoanStage;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /** The 5 fixed pipeline stages. */
    private const STAGES = [
        'Pengajuan Diterima',
        'Verifikasi Dokumen',
        'Review Kredit',
        'Persetujuan',
        'Dana Dicairkan',
    ];

    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@mikrolink.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'role' => 'Admin Koperasi']
        );

        $anggota = User::firstOrCreate(
            ['email' => 'anggota@mikrolink.com'],
            ['name' => 'Anggota User', 'password' => bcrypt('password'), 'role' => 'user']
        );

        // Seed some extra users for variety
        $extraUsers = User::factory(3)->create(['role' => 'user']);

        $dummy = [
            // Loan 1 — Anggota, 100% (Disetujui)
            [
                'user'             => $anggota,
                'type'             => 'Pinjaman Usaha',
                'amount'           => 15_000_000,
                'duration'         => 12,
                'reason'           => 'Modal usaha warung makan.',
                'completed_stages' => 5,
            ],
            // Loan 2 — Extra user, 75% (Dalam Review)
            [
                'user'             => $extraUsers[0],
                'type'             => 'Pinjaman Usaha',
                'amount'           => 10_000_000,
                'duration'         => 12,
                'reason'           => 'Pengembangan toko kelontong.',
                'completed_stages' => 3,
            ],
            // Loan 3 — Extra user, 100% (Disetujui)
            [
                'user'             => $extraUsers[1],
                'type'             => 'Pinjaman Usaha',
                'amount'           => 15_000_000,
                'duration'         => 24,
                'reason'           => 'Pembelian mesin jahit untuk usaha konveksi.',
                'completed_stages' => 5,
            ],
            // Loan 4 — Extra user, 0% (Baru)
            [
                'user'             => $extraUsers[2],
                'type'             => 'Pinjaman Konsumsi',
                'amount'           => 10_000_000,
                'duration'         => 6,
                'reason'           => 'Kebutuhan biaya pendidikan anak.',
                'completed_stages' => 0,
            ],
            // Loan 5 — Anggota, 40% (Dalam Review)
            [
                'user'             => $anggota,
                'type'             => 'Pinjaman Darurat',
                'amount'           => 5_000_000,
                'duration'         => 6,
                'reason'           => 'Biaya pengobatan anggota keluarga.',
                'completed_stages' => 2,
            ],
        ];

        foreach ($dummy as $index => $data) {
            $loan = Loan::create([
                'user_id'             => $data['user']->id,
                'type'                => $data['type'],
                'amount'              => $data['amount'],
                'duration'            => $data['duration'],
                'reason'              => $data['reason'],
                'status'              => 'Baru',
                'progress_percentage' => 0,
            ]);

            foreach (self::STAGES as $order => $stageName) {
                $stageOrder   = $order + 1;
                $isCompleted  = $stageOrder <= $data['completed_stages'];
                $completedAt  = $isCompleted ? now()->subDays(5 - $order) : null;
                $completedBy  = ($isCompleted && $admin) ? $admin->id : null;

                LoanStage::create([
                    'loan_id'         => $loan->id,
                    'stage_order'     => $stageOrder,
                    'stage_name'      => $stageName,
                    'completed'       => $isCompleted,
                    'completed_at'    => $completedAt,
                    'completed_by_id' => $completedBy,
                    'notes'           => null,
                ]);
            }

            // Recalculate progress from stages
            $loan->refresh()->recalculateProgress();
        }
    }
}
