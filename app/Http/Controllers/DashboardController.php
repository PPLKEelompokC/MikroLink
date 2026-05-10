<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\Deposit;
use App\Models\FinancialRecord;
use App\Models\FundAllocation;
use App\Models\Koperasi;
use App\Models\Loan;
use App\Models\TrustMetric;
use App\Models\LiterasiArtikel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->role === 'user') {

            // --- Trust Score ---
            $trustMetric = $user->trustMetric;
            $trustScore = $trustMetric ? $trustMetric->final_index : 50;

            // Optimization: Remove Schema::hasTable for performance, assume table exists
            $dbTrustScore = DB::table('trust_metrics')
                ->where('user_id', $user->id)
                ->value('score');
            
            if ($dbTrustScore !== null) {
                $trustScore = $dbTrustScore;
            }

            // --- Simpanan (Optimization: Single Query) ---
            $simpananTotals = $user->getAllSimpananTotals();
            $simpananPokok    = $simpananTotals['POKOK'] ?? 0;
            $simpananWajib    = $simpananTotals['WAJIB'] ?? 0;
            $simpananSukarela = $simpananTotals['SUKARELA'] ?? 0;

            // --- Aspirasi Terbaru ---
            $userAspirations = Aspiration::where('user_id', $user->id)
                ->latest()
                ->take(3)
                ->get();

            // --- KYC Status (Optimization) ---
            $kycStatus = DB::table('community_documents')
                ->where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists() ? 'VERIFIED' : 'PENDING';

            // --- Return View ---
            $artikelTerbaru = LiterasiArtikel::published()->latest()->take(3)->get();

            return view('dashboard', compact(
                'trustMetric',
                'trustScore',
                'simpananPokok',
                'simpananWajib',
                'simpananSukarela',
                'userAspirations',
                'kycStatus',
                'artikelTerbaru',
            ));
        }

        // --- Admin / Manajer Logic ---
        
        // PBI-02 Optimization: Caching Koperasi Profile for 60 minutes
        $koperasi = Cache::remember('koperasi_profile', 3600, function () {
            return Koperasi::firstOrCreate(
                ['id_koperasi' => 'KOP-001'],
                [
                    'nama_koperasi' => 'Koperasi MikroLink',
                    'alamat'        => 'Jl. Merdeka No 1',
                    'saldo_kas'     => 350500000,
                ]
            );
        });

        $availableCapital   = $koperasi->saldo_kas;
        $likuiditas         = $koperasi->cekLikuiditas();
        
        // Optimization: Don't load all logs, use count() and latest()
        $totalTransaksi     = $koperasi->capitalLogs()->count();
        $latestLog          = $koperasi->capitalLogs()->latest()->first();
        $terakhirDiperbarui = $latestLog ? $latestLog->created_at->diffForHumans() : 'Belum ada transaksi';
        
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

        // --- Badge Pending Counts ---
        $pendingDepositsCount = Deposit::where('status', 'PENDING')->count();
        $pendingAllocationsCount = FundAllocation::where('status', 'pending')->count();

        // --- Badge Pinjaman ✅ ---
        $pendingLoansCount        = Loan::where('status', 'Baru')->count();
        $pendingManajerLoansCount = Loan::where('status', 'Dalam Review')->count();

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
            'pendingAllocationsCount',
            'pendingLoansCount',           
            'pendingManajerLoansCount',    
        ));
    }
}