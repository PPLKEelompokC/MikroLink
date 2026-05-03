<?php

namespace App\Jobs;

use App\Models\FundAllocation;
use App\Models\IdleFundSnapshot;
use App\Models\Koperasi;
use App\Services\AiAllocationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AnalyzeIdleFundsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 2;

    /**
     * Number of seconds the job can run before timing out.
     */
    public int $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Koperasi $koperasi,
        public bool $skipDuplicateCheck = false,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AiAllocationService $aiAllocationService): void
    {
        // Duplicate guard: skip if allocations already exist for today's snapshot
        if (! $this->skipDuplicateCheck) {
            $existingSnapshot = IdleFundSnapshot::where('koperasi_id', $this->koperasi->id_koperasi)
                ->where('snapshot_date', now()->toDateString())
                ->first();

            if ($existingSnapshot) {
                $hasAllocations = FundAllocation::where('snapshot_id', $existingSnapshot->id)->exists();

                if ($hasAllocations) {
                    Log::info('Skipping duplicate fund allocation analysis — allocations already exist for today.', [
                        'koperasi_id' => $this->koperasi->id_koperasi,
                        'snapshot_id' => $existingSnapshot->id,
                    ]);

                    return;
                }
            }
        }

        $allocations = $aiAllocationService->analyze($this->koperasi);

        Log::info('Fund allocation analysis completed via job.', [
            'koperasi_id' => $this->koperasi->id_koperasi,
            'allocation_count' => $allocations->count(),
        ]);
    }
}
