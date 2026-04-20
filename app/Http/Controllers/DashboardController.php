<?php

namespace App\Http\Controllers;

use App\Models\Koperasi;

class DashboardController extends Controller
{
    public function index()
    {
        $koperasi = Koperasi::with(['capitalLogs', 'financialRecords'])->firstOrCreate(
            ['id_koperasi' => 'KOP-001'],
            ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
        );

        $availableCapital = $koperasi->saldo_kas;
        $likuiditas = $koperasi->cekLikuiditas();
        $totalTransaksi = $koperasi->capitalLogs->count();
        $terakhirDiperbarui = $koperasi->capitalLogs->last() ? $koperasi->capitalLogs->last()->created_at->diffForHumans() : 'Belum ada transaksi';
        $capitalLogs = $koperasi->capitalLogs()->latest()->take(5)->get();

        // Financial chart data
        $financialRecords = $koperasi->financialRecords()
            ->orderBy('record_date', 'asc')
            ->get();

        $chartLabels = $financialRecords->map(fn ($record) => $record->record_date->translatedFormat('M Y'))->values()->toArray();
        $omzetData = $financialRecords->pluck('omzet')->values()->toArray();
        $creditScoreData = $financialRecords->pluck('credit_score')->values()->toArray();

        // Calculate latest values for tooltip display
        $latestOmzet = $financialRecords->last()?->omzet ?? 0;
        $latestCreditScore = $financialRecords->last()?->credit_score ?? 0;

        // Calculate omzet growth percentage (normalized to max for chart display)
        $maxOmzet = max($omzetData ?: [1]);
        $omzetPercentage = $maxOmzet > 0 ? round(($latestOmzet / $maxOmzet) * 100, 1) : 0;

        return view('dashboard', compact(
            'koperasi',
            'availableCapital',
            'likuiditas',
            'totalTransaksi',
            'terakhirDiperbarui',
            'capitalLogs',
            'chartLabels',
            'omzetData',
            'creditScoreData',
            'omzetPercentage',
            'latestCreditScore',
        ));
    }
}
