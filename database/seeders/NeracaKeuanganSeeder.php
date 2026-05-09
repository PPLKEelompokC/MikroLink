<?php

namespace Database\Seeders;

use App\Models\Koperasi;
use App\Models\NeracaKeuangan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class NeracaKeuanganSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan koperasi ada
        $koperasi = Koperasi::firstOrCreate(
            ['id_koperasi' => 'KOP-001'],
            [
                'nama_koperasi'           => 'Koperasi MikroLink',
                'alamat'                  => 'Jl. Merdeka No. 1, Bandung',
                'saldo_kas'               => 350_500_000,
                'total_outstanding_loans' => 120_000_000,
            ]
        );

        // Data 12 bulan terakhir — tumbuh realistis setiap bulan
        $bulanData = [
            // [simpanan_pokok, simpanan_wajib, simpanan_sukarela, piutang, kewajiban_tarik, kas_bulan]
            [-12, 45_000_000, 28_000_000, 12_000_000,  80_000_000,  2_500_000, 180_000_000],
            [-11, 52_000_000, 31_000_000, 14_500_000,  88_000_000,  3_000_000, 205_000_000],
            [-10, 58_000_000, 34_000_000, 17_000_000,  92_000_000,  1_800_000, 225_000_000],
            [-9,  63_000_000, 37_000_000, 20_000_000,  98_000_000,  4_200_000, 248_000_000],
            [-8,  68_000_000, 40_000_000, 23_500_000, 105_000_000,  3_500_000, 270_000_000],
            [-7,  72_000_000, 43_000_000, 27_000_000, 110_000_000,  2_100_000, 285_000_000],
            [-6,  77_000_000, 46_000_000, 30_500_000, 112_000_000,  5_000_000, 300_000_000],
            [-5,  82_000_000, 49_000_000, 33_000_000, 115_000_000,  3_800_000, 315_000_000],
            [-4,  87_000_000, 52_000_000, 36_000_000, 118_000_000,  2_500_000, 328_000_000],
            [-3,  91_000_000, 54_500_000, 38_500_000, 119_000_000,  4_100_000, 338_000_000],
            [-2,  95_000_000, 57_000_000, 41_000_000, 120_000_000,  3_200_000, 345_000_000],
            [-1,  98_500_000, 59_500_000, 44_000_000, 120_000_000,  2_800_000, 350_500_000],
        ];

        foreach ($bulanData as [$offset, $pokok, $wajib, $sukarela, $piutang, $kwjb, $kas]) {
            $periode   = Carbon::now()->addMonths($offset)->endOfMonth();
            $periodeLabel = Carbon::now()->addMonths($offset)->locale('id')->translatedFormat('F Y');

            $totalAset      = $kas + $piutang;
            $modalDisetor   = $pokok + $wajib;
            $shu            = max(0, $totalAset - $kwjb - $modalDisetor);
            $totalEkuitas   = $modalDisetor + $shu;
            $totalKewajiban = $kwjb;

            $rasioLikuiditas = $totalKewajiban > 0
                ? round(($kas / $totalKewajiban) * 100, 2)
                : 200.00;

            $rasioKecukupan = $totalAset > 0
                ? round(($totalEkuitas / $totalAset) * 100, 2)
                : 0.00;

            NeracaKeuangan::updateOrCreate(
                [
                    'koperasi_id' => $koperasi->id_koperasi,
                    'periode'     => $periode->toDateString(),
                ],
                [
                    'periode_label'           => $periodeLabel,
                    'kas'                     => $kas,
                    'simpanan_pokok'          => $pokok,
                    'simpanan_wajib'          => $wajib,
                    'simpanan_sukarela'       => $sukarela,
                    'piutang_pinjaman'        => $piutang,
                    'total_aset'              => $totalAset,
                    'kewajiban_tarik_pending' => $kwjb,
                    'total_kewajiban'         => $totalKewajiban,
                    'modal_disetor'           => $modalDisetor,
                    'sisa_hasil_usaha'        => $shu,
                    'total_ekuitas'           => $totalEkuitas,
                    'rasio_likuiditas'        => $rasioLikuiditas,
                    'rasio_kecukupan'         => $rasioKecukupan,
                    'is_auto'                 => true,
                    'generated_by'            => null,
                ]
            );
        }

        $this->command->info('✅ Neraca Keuangan: 12 bulan data seeded for KOP-001');
    }
}