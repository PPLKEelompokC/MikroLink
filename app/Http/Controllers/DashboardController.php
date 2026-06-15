<?php

namespace App\Http\Controllers;

use App\Models\Aspiration;
use App\Models\Deposit;
use App\Models\FundAllocation;
use App\Models\Koperasi;
use App\Models\LiterasiArtikel;
use App\Models\Loan;
use App\Models\NeracaKeuangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->role === 'user') {

            // --- Trust Score ---
            $trustMetric = $user->trustMetric;
            $trustScore = $trustMetric ? $trustMetric->final_index : 50;

            $dbTrustScore = Cache::remember("trust_score_{$user->id}", 300, function () use ($user) {
                return DB::table('trust_metrics')
                    ->where('user_id', $user->id)
                    ->value('score');
            });

            if ($dbTrustScore !== null) {
                $trustScore = $dbTrustScore;
            }

            // --- Simpanan (cached 5 menit) ---
            $simpananTotals = Cache::remember("simpanan_totals_{$user->id}", 300, function () use ($user) {
                return $user->getAllSimpananTotals();
            });
            $simpananPokok = $simpananTotals['POKOK'] ?? 0;
            $simpananWajib = $simpananTotals['WAJIB'] ?? 0;
            $simpananSukarela = $simpananTotals['SUKARELA'] ?? 0;

            // --- Aspirasi Terbaru (cached 2 menit) ---
            $userAspirations = Cache::remember("user_aspirations_{$user->id}", 120, function () use ($user) {
                return Aspiration::where('user_id', $user->id)
                    ->latest()
                    ->take(3)
                    ->get();
            });

            // --- KYC Status (cached 10 menit) ---
            $kycStatus = Cache::remember("kyc_status_{$user->id}", 600, function () use ($user) {
                $hasCommunityDoc = DB::table('community_documents')
                    ->where('user_id', $user->id)
                    ->where('status', 'approved')
                    ->exists();

                $hasKycVerification = DB::table('kyc_verifications')
                    ->where('user_id', $user->id)
                    ->where('status', 'APPROVED')
                    ->exists();

                return ($hasCommunityDoc || $hasKycVerification) ? 'VERIFIED' : 'PENDING';
            });

            // --- Chart Data (Data Pribadi User) ---
            $chartData = Cache::remember("dashboard_chart_data_user_{$user->id}", 60, function () use ($user) {
                $labels = [];
                $depositData = [];
                $withdrawalData = [];

                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $labels[] = $date->translatedFormat('M Y');
                    
                    $depositData[] = (float) \App\Models\Deposit::where('user_id', $user->id)
                        ->where('status', 'APPROVED')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->sum('amount');
                        
                    $withdrawalData[] = (float) \App\Models\Withdrawal::where('user_id', $user->id)
                        ->where('status', 'APPROVED')
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->sum('amount');
                }

                return [
                    'labels' => $labels,
                    'depositData' => $depositData,
                    'withdrawalData' => $withdrawalData,
                ];
            });

            $chartLabels = $chartData['labels'];
            $depositData = $chartData['depositData'];
            $withdrawalData = $chartData['withdrawalData'];

            // --- Artikel Terbaru (cached 10 menit) ---
            $artikelTerbaru = Cache::remember('literasi_terbaru', 600, function () {
                return LiterasiArtikel::published()->latest()->take(3)->get();
            });

            return view('dashboard', compact(
                'trustMetric',
                'trustScore',
                'simpananPokok',
                'simpananWajib',
                'simpananSukarela',
                'userAspirations',
                'kycStatus',
                'artikelTerbaru',
                'chartLabels',
                'depositData',
                'withdrawalData',
            ));
        }

        // --- Admin / Manajer Logic ---

        $koperasi = Cache::remember('koperasi_kop001', 300, function () {
            return Koperasi::firstOrCreate(
                ['id_koperasi' => 'KOP-001'],
                [
                    'nama_koperasi' => 'Koperasi MikroLink',
                    'alamat' => 'Jl. Merdeka No 1',
                    'saldo_kas' => 350500000,
                ]
            );
        });

        $availableCapital = $koperasi->saldo_kas;
        $likuiditas = $koperasi->cekLikuiditas();

        // --- Capital Logs (cached 2 menit) ---
        $capitalStats = Cache::remember('capital_stats_kop001', 120, function () use ($koperasi) {
            $count = $koperasi->capitalLogs()->count();
            $latestLog = $koperasi->capitalLogs()->latest()->first();

            return [
                'count' => $count,
                'latestLog' => $latestLog,
                'recentLogs' => $koperasi->capitalLogs()->latest()->take(5)->get(),
            ];
        });

        $totalTransaksi = $capitalStats['count'];
        $terakhirDiperbarui = $capitalStats['latestLog']
            ? $capitalStats['latestLog']->created_at->diffForHumans()
            : 'Belum ada transaksi';
        $capitalLogs = $capitalStats['recentLogs'];

        // --- Chart Data (cached 10 menit) ---
        $chartData = Cache::remember('dashboard_chart_data_admin', 600, function () use ($koperasi) {
            $records = NeracaKeuangan::where('koperasi_id', $koperasi->id_koperasi)
                ->orderBy('periode', 'asc')
                ->get();

            $omzetData = $records->pluck('total_aset')->map(fn ($v) => (float) $v)->values()->toArray();
            $creditScoreData = $records->map(function ($r) {
                $totalAset = (float) $r->total_aset;
                $sukarela = (float) $r->simpanan_sukarela;

                return $totalAset > 0 ? min(100, round($sukarela / $totalAset * 100, 1)) : 0;
            })->values()->toArray();

            $latestOmzet = (float) ($records->last()?->total_aset ?? 0);
            $maxOmzet = max($omzetData ?: [1]);

            return [
                'labels' => $records->pluck('periode_label')->values()->toArray(),
                'omzetData' => $omzetData,
                'creditScoreData' => $creditScoreData,
                'omzetPercentage' => $maxOmzet > 0 ? round(($latestOmzet / $maxOmzet) * 100, 1) : 0,
                'latestCreditScore' => end($creditScoreData) ?: 0,
            ];
        });

        $chartLabels = $chartData['labels'];
        $omzetData = $chartData['omzetData'];
        $creditScoreData = $chartData['creditScoreData'];
        $omzetPercentage = $chartData['omzetPercentage'];
        $latestCreditScore = $chartData['latestCreditScore'];

        // --- Badge Pending Counts (cached 2 menit) ---
        $pendingCounts = Cache::remember('pending_counts_admin', 120, function () {
            return [
                'deposits' => Deposit::where('status', 'PENDING')->count(),
                'allocations' => FundAllocation::where('status', 'pending')->count(),
                'loans' => Loan::where('status', 'Baru')->count(),
                'manajerLoans' => Loan::where('status', 'Dalam Review')->count(),
            ];
        });

        $pendingDepositsCount = $pendingCounts['deposits'];
        $pendingAllocationsCount = $pendingCounts['allocations'];
        $pendingLoansCount = $pendingCounts['loans'];
        $pendingManajerLoansCount = $pendingCounts['manajerLoans'];

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
