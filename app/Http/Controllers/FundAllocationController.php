<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessAiFundAllocation;
use App\Models\FundAllocation;
use App\Models\Koperasi;
use App\Services\AiAllocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FundAllocationController extends Controller
{
    public function __construct(private AiAllocationService $aiAllocationService) {}

    /**
     * List all fund allocation recommendations.
     */
    public function index(Request $request): View
    {
        $query = FundAllocation::with(['snapshot', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $allocations = $query->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.fund-allocation.index', compact('allocations'));
    }

    /**
     * Show a single fund allocation recommendation.
     */
    public function show(FundAllocation $fundAllocation): View
    {
        $fundAllocation->load(['snapshot', 'koperasi', 'reviewer']);

        return view('admin.fund-allocation.show', compact('fundAllocation'));
    }

    /**
     * Trigger AI analysis for the cooperative's idle funds.
     */
    public function triggerAnalysis(): RedirectResponse
    {
        $koperasi = Koperasi::firstOrFail();

        // 1. Guard: Check if already analyzed today
        if ($this->aiAllocationService->hasRecentAnalysis($koperasi)) {
            return redirect()
                ->route('admin.fund-allocation.index')
                ->with('error', 'Analisis untuk hari ini sudah dijalankan. Silakan cek daftar rekomendasi di bawah.');
        }

        try {
            // 2. Dispatch async job
            ProcessAiFundAllocation::dispatch($koperasi);

            return redirect()
                ->route('admin.fund-allocation.index')
                ->with('success', 'Analisis AI sedang berjalan di latar belakang. Silakan tunggu beberapa saat dan refresh halaman ini.');
        } catch (\Exception $exception) {
            return redirect()
                ->route('admin.fund-allocation.index')
                ->with('error', 'Gagal menjadwalkan analisis AI: '.$exception->getMessage());
        }
    }

    /**
     * Update the status of a fund allocation recommendation (approve/reject).
     */
    public function updateStatus(Request $request, FundAllocation $fundAllocation): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        $fundAllocation->update([
            'status' => $validated['status'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        $statusLabel = $validated['status'] === 'approved' ? 'disetujui' : 'ditolak';

        return redirect()
            ->route('admin.fund-allocation.show', $fundAllocation)
            ->with('success', "Rekomendasi alokasi telah {$statusLabel}.");
    }
}
