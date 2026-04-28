<?php

namespace App\Http\Controllers;

use App\Jobs\AnalyzeIdleFundsJob;
use App\Models\FundAllocation;
use App\Models\IdleFundSnapshot;
use App\Models\Koperasi;
use App\Services\AiAllocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FundAllocationController extends Controller
{
    public function __construct(private AiAllocationService $aiAllocationService) {}

    /**
     * List all fund allocation recommendations with optional status filter.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');

        $allocations = FundAllocation::with(['snapshot', 'reviewer'])
            ->when($statusFilter && in_array($statusFilter, ['pending', 'approved', 'rejected']), function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $statusCounts = [
            'all' => FundAllocation::count(),
            'pending' => FundAllocation::where('status', 'pending')->count(),
            'approved' => FundAllocation::where('status', 'approved')->count(),
            'rejected' => FundAllocation::where('status', 'rejected')->count(),
        ];

        return view('admin.fund-allocation.index', compact('allocations', 'statusFilter', 'statusCounts'));
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
     * Trigger AI analysis for the cooperative's idle funds (dispatched to queue).
     */
    public function triggerAnalysis(Request $request): RedirectResponse
    {
        $koperasi = Koperasi::firstOrFail();

        // Check for existing analysis today
        $existingSnapshot = IdleFundSnapshot::where('koperasi_id', $koperasi->id_koperasi)
            ->where('snapshot_date', now()->toDateString())
            ->first();

        if ($existingSnapshot) {
            $hasAllocations = FundAllocation::where('snapshot_id', $existingSnapshot->id)->exists();

            if ($hasAllocations && ! $request->has('force')) {
                return redirect()
                    ->route('admin.fund-allocation.index')
                    ->with('error', 'Analisis untuk hari ini sudah pernah dijalankan. Gunakan tombol "Jalankan Ulang" jika ingin menganalisis ulang.');
            }
        }

        // Dispatch with skipDuplicateCheck since we already checked
        AnalyzeIdleFundsJob::dispatchSync($koperasi, skipDuplicateCheck: true);

        return redirect()
            ->route('admin.fund-allocation.index')
            ->with('success', 'Analisis AI selesai. Rekomendasi baru telah ditambahkan.');
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
