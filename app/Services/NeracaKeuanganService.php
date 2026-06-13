<?php

namespace App\Services;

use App\Models\CapitalLog;
use App\Models\Koperasi;
use App\Models\Loan;
use App\Models\NeracaKeuangan;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class NeracaKeuanganService
{
    /**
     * Generate neraca otomatis untuk bulan tertentu.
     * Jika sudah ada → update. Jika belum → insert.
     */
    public function generate(Koperasi $koperasi, Carbon $periode): NeracaKeuangan
    {
        $endOfMonth = $periode->copy()->endOfMonth();

        // ── ASET ────────────────────────────────────────────────
        $kas = (float) $koperasi->saldo_kas;

        $simpananPokok    = $this->totalSimpanan('simpanan_pokok',    $endOfMonth, $koperasi);
        $simpananWajib    = $this->totalSimpanan('simpanan_wajib',    $endOfMonth, $koperasi);
        $simpananSukarela = $this->totalSimpanan('simpanan_sukarela', $endOfMonth, $koperasi);

        // Jika tidak ada data simpanan sama sekali, gunakan estimasi dari kas
        if ($simpananPokok === 0.0 && $simpananWajib === 0.0 && $simpananSukarela === 0.0) {
            $simpananPokok    = round($kas * 0.28, 2);
            $simpananWajib    = round($kas * 0.17, 2);
            $simpananSukarela = round($kas * 0.13, 2);
        }

        $piutangPinjaman = 0.0;
        if (Schema::hasTable('loans')) {
            $piutangPinjaman = (float) Loan::where('status', 'Disetujui')
                ->whereDate('created_at', '<=', $endOfMonth)
                ->sum('amount');
        }
        // Fallback: gunakan total_outstanding_loans dari koperasi jika ada
        if ($piutangPinjaman === 0.0 && isset($koperasi->total_outstanding_loans)) {
            $piutangPinjaman = (float) $koperasi->total_outstanding_loans;
        }

        $totalAset = $kas + $piutangPinjaman;

        // ── KEWAJIBAN ────────────────────────────────────────────
        $kewajibanTarik = 0.0;
        if (Schema::hasTable('withdrawals')) {
            $kewajibanTarik = (float) Withdrawal::where('status', 'PENDING')
                ->whereDate('created_at', '<=', $endOfMonth)
                ->sum('amount');
        }
        $totalKewajiban = $kewajibanTarik;

        // ── EKUITAS ──────────────────────────────────────────────
        $modalDisetor = $simpananPokok + $simpananWajib;
        $shu          = max(0, $totalAset - $totalKewajiban - $modalDisetor);
        $totalEkuitas = $modalDisetor + $shu;

        // ── RASIO ────────────────────────────────────────────────
        $rasioLikuiditas = $totalKewajiban > 0
            ? round(($kas / $totalKewajiban) * 100, 2)
            : ($kas > 0 ? 200.00 : 0.00);

        $rasioKecukupan = $totalAset > 0
            ? round(($totalEkuitas / $totalAset) * 100, 2)
            : 0.00;

        // Periode label dalam Bahasa Indonesia
        $periodeLabel = $periode->locale('id')->translatedFormat('F Y');

        $periodeKey = $endOfMonth->toDateString();

        $neraca = NeracaKeuangan::where('koperasi_id', $koperasi->id_koperasi)
            ->whereDate('periode', $periodeKey)
            ->first();

        $data = [
            'periode_label'           => $periodeLabel,
            'kas'                     => $kas,
            'simpanan_pokok'          => $simpananPokok,
            'simpanan_wajib'          => $simpananWajib,
            'simpanan_sukarela'       => $simpananSukarela,
            'piutang_pinjaman'        => $piutangPinjaman,
            'total_aset'              => $totalAset,
            'kewajiban_tarik_pending' => $kewajibanTarik,
            'total_kewajiban'         => $totalKewajiban,
            'modal_disetor'           => $modalDisetor,
            'sisa_hasil_usaha'        => $shu,
            'total_ekuitas'           => $totalEkuitas,
            'rasio_likuiditas'        => $rasioLikuiditas,
            'rasio_kecukupan'         => $rasioKecukupan,
            'is_auto'                 => true,
            'generated_by'            => Auth::id(),
        ];

        if ($neraca) {
            $neraca->update($data);
            return $neraca;
        }

        return NeracaKeuangan::create(array_merge([
            'koperasi_id' => $koperasi->id_koperasi,
            'periode'     => $periodeKey,
        ], $data));
    }

    /** Generate 6 bulan terakhir sekaligus */
    public function generateLast6Months(Koperasi $koperasi): array
    {
        $results = [];
        for ($i = 5; $i >= 0; $i--) {
            $periode  = Carbon::now()->subMonths($i)->startOfMonth();
            $results[] = $this->generate($koperasi, $periode);
        }
        return $results;
    }

    private function totalSimpanan(string $type, Carbon $date, Koperasi $koperasi): float
    {
        $depositSum = (float) CapitalLog::where('koperasi_id', $koperasi->id_koperasi)
            ->where('type', $type)
            ->where('transaction_type', 'deposit')
            ->whereIn('status', ['Selesai', 'Disetujui'])
            ->whereDate('created_at', '<=', $date)
            ->sum('amount');

        $withdrawalSum = (float) CapitalLog::where('koperasi_id', $koperasi->id_koperasi)
            ->where('type', $type)
            ->where('transaction_type', 'withdrawal')
            ->whereIn('status', ['Selesai', 'Disetujui'])
            ->whereDate('created_at', '<=', $date)
            ->sum('amount');

        return max(0.0, $depositSum - $withdrawalSum);
    }
}