<?php

namespace App\Http\Controllers;

use App\Models\Koperasi;
use App\Models\Aspiration;
use App\Models\Deposit;
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

            // --- Trust Score ---
            $trustMetric = $user->trustMetric;
            $trustScore  = $trustMetric ? $trustMetric->final_index : 50;

            if (Schema::hasTable('trust_metrics')) {
                $trustScore = DB::table('trust_metrics')
                    ->where('user_id', $user->id)
                    ->value('score') ?? $trustScore;
            }

            // --- Simpanan (dari tabel deposits) ---
            $simpananPokok    = $user->totalSimpanan('POKOK');
            $simpananWajib    = $user->totalSimpanan('WAJIB');
            $simpananSukarela = $user->totalSimpanan('SUKARELA');

            // --- Aspirasi Terbaru ---
            $userAspirations = Aspiration::where('user_id', $user->id)
                ->latest()
                ->take(3)
                ->get();

            // --- KYC Status ---
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

        // --- Admin / Manajer Logic ---
        $koperasi = Koperasi::with(['capitalLogs', 'financialRecords'])->firstOrCreate(
            ['id_koperasi' => 'KOP-001'],
            [
                'nama_koperasi' => 'Koperasi MikroLink',
                'alamat'        => 'Jl. Merdeka No 1',
                'saldo_kas'     => 350500000,
            ]
        );

        $availableCapital    = $koperasi->saldo_kas;
        $likuiditas          = $koperasi->cekLikuiditas();
        $totalTransaksi      = $koperasi->capitalLogs->count();
        $terakhirDiperbarui  = $koperasi->capitalLogs->last()
            ? $koperasi->capitalLogs->last()->created_at->diffForHumans()
            : 'Belum ada transaksi';
        $capitalLogs = $koperasi->capitalLogs()->latest()->take(5)->get();

        // --- Chart Data ---
        $financialRecords = $koperasi->financialRecords()
            ->orderBy('record_date', 'asc')
            ->get();

        $chartLabels     = $financialRecords->map(fn ($r) => $r->record_date->translatedFormat('M Y'))->values()->toArray();
        $omzetData       = $financialRecords->pluck('omzet')->values()->toArray();
        $creditScoreData = $financialRecords->pluck('credit_score')->values()->toArray();

        $latestOmzet       = $financialRecords->last()?->omzet ?? 0;
        $latestCreditScore = $financialRecords->last()?->credit_score ?? 0;

        $maxOmzet        = max($omzetData ?: [1]);
        $omzetPercentage = $maxOmzet > 0 ? round(($latestOmzet / $maxOmzet) * 100, 1) : 0;

        // --- Badge Setoran Pending untuk Admin ---
        $pendingDepositsCount = Deposit::where('status', 'PENDING')->count();

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
            'pendingDepositsCount',
        ));
    }
}