<?php

namespace App\Http\Controllers;

use App\Models\FundAllocation;
use App\Models\IdleFundSnapshot;
use App\Models\Koperasi;
use App\Models\Loan;
use App\Models\TrustMetric;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SystemRequirementController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('system-requirements.eligibility');
    }

    public function eligibility(): View
    {
        $trustMetrics = TrustMetric::with('user')
            ->latest()
            ->get();

        $latestLoans = Loan::query()
            ->whereIn('user_id', $trustMetrics->pluck('user_id'))
            ->latest()
            ->get()
            ->groupBy('user_id')
            ->map(fn ($loans) => $loans->first());

        $members = $trustMetrics->map(function (TrustMetric $trustMetric) use ($latestLoans): array {
            $loan = $latestLoans->get($trustMetric->user_id);

            return [
                'name' => $trustMetric->user?->name ?? 'Anggota Koperasi',
                'loan_id_number' => $loan?->loan_id_number,
                'loan_type' => $loan?->type,
                'loan_status' => $loan?->status,
                'participation_score' => $trustMetric->participation_score,
                'integrity_score' => $trustMetric->integrity_score,
                'reliability_score' => $trustMetric->reliability_score,
                'final_index' => $trustMetric->final_index,
                'notes' => $trustMetric->notes,
            ];
        });

        $summary = [
            'total_members' => $members->count(),
            'average_score' => $members->isNotEmpty() ? round((float) $members->avg('final_index'), 1) : null,
            'with_active_submission' => $members->whereNotNull('loan_id_number')->count(),
            'data_source' => 'trust_metrics, loans',
        ];

        return view('system-requirements.eligibility', compact('members', 'summary'));
    }

    public function approvals(): View
    {
        $requests = Loan::with(['user', 'adminReviewer', 'manajerReviewer'])
            ->latest()
            ->get()
            ->map(function (Loan $loan): array {
                $adminApproved = filled($loan->reviewed_by_admin);
                $managerApproved = filled($loan->reviewed_by_manajer);
                $disbursementLocked = ! ($loan->status === 'Disetujui' && $adminApproved && $managerApproved);

                return [
                    'loan_id_number' => $loan->loan_id_number,
                    'member' => $loan->user?->name ?? 'Anggota Koperasi',
                    'type' => $loan->type,
                    'amount' => $loan->amount,
                    'status' => $loan->status,
                    'stage' => $this->approvalStage($loan, $adminApproved, $managerApproved),
                    'admin_reviewer' => $loan->adminReviewer?->name,
                    'manager_reviewer' => $loan->manajerReviewer?->name,
                    'is_disbursement_locked' => $disbursementLocked,
                    'submitted_at' => $loan->created_at?->format('d M Y'),
                ];
            });

        $summary = [
            'total_requests' => $requests->count(),
            'locked_count' => $requests->where('is_disbursement_locked', true)->count(),
            'ready_count' => $requests->where('is_disbursement_locked', false)->count(),
        ];

        $workflow = [
            ['step' => 'Admin', 'rule' => 'Akun Admin melakukan persetujuan awal pengajuan pinjaman.'],
            ['step' => 'Manajer', 'rule' => 'Akun Manajer melakukan persetujuan lanjutan setelah Admin.'],
            ['step' => 'Pencairan', 'rule' => 'Fungsi pencairan terkunci hingga mendapatkan persetujuan dari akun Admin dan Manajer.'],
        ];

        return view('system-requirements.approvals', compact('requests', 'workflow', 'summary'));
    }

    public function allocation(): View
    {
        $koperasi = Koperasi::first();
        $latestSnapshot = IdleFundSnapshot::with('koperasi')
            ->latest('snapshot_date')
            ->first();

        $allocations = FundAllocation::with(['koperasi', 'snapshot', 'reviewer'])
            ->latest()
            ->get();

        $summary = [
            'total_recommendations' => $allocations->count(),
            'pending_count' => $allocations->where('status', 'pending')->count(),
            'approved_count' => $allocations->where('status', 'approved')->count(),
            'rejected_count' => $allocations->where('status', 'rejected')->count(),
        ];

        return view('system-requirements.allocation', compact('koperasi', 'latestSnapshot', 'allocations', 'summary'));
    }

    private function approvalStage(Loan $loan, bool $adminApproved, bool $managerApproved): string
    {
        if ($loan->status === 'Ditolak') {
            return 'Ditolak';
        }

        if ($adminApproved && $managerApproved) {
            return 'Mendapatkan persetujuan Admin dan Manajer';
        }

        if ($adminApproved) {
            return 'Menunggu persetujuan Manajer';
        }

        return 'Menunggu persetujuan Admin';
    }
}
