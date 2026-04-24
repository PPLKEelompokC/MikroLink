<?php

namespace App\Http\Controllers;

use App\Models\Koperasi;
use App\Models\Aspiration;
use App\Models\TrustMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user && $user->role === 'user') {
            // Data for Member (User)
            $trustScore = 50; 
            $simpananPokok = 0;
            $simpananWajib = 0;
            $simpananSukarela = 0;

            $trustMetric = $user->trustMetric;
            $trustScore = $trustMetric ? $trustMetric->final_index : 50;
            
            if (Schema::hasTable('simpanans')) {
                $simpananData = DB::table('simpanans')
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->select('jenis_simpanan', DB::raw('SUM(nominal) as total'))
                    ->groupBy('jenis_simpanan')
                    ->pluck('total', 'jenis_simpanan');

                $simpananPokok = $simpananData->get('Pokok', 0);
                $simpananWajib = $simpananData->get('Wajib', 0);
                $simpananSukarela = $simpananData->get('Sukarela', 0);
            }

            $userAspirations = Aspiration::where('user_id', $user->id)
                ->latest()
                ->take(3)
                ->get();

            $kycStatus = 'PENDING';
            if (Schema::hasTable('community_documents')) {
                $kycStatus = DB::table('community_documents')
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->exists() ? 'VERIFIED' : 'PENDING';
            }

            return view('dashboard', compact(
                'trustMetric',
                'trustScore',
                'simpananPokok',
                'simpananWajib',
                'simpananSukarela',
                'userAspirations',
                'kycStatus'
            ));
        }

        // --- Existing Admin Logic ---
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
