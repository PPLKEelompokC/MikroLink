<?php

namespace App\Jobs;

use App\Models\Koperasi;
use App\Services\AiAllocationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessAiFundAllocation implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Koperasi $koperasi) {}

    /**
     * Execute the job.
     */
    public function handle(AiAllocationService $aiAllocationService): void
    {
        Log::info("Starting async AI fund allocation analysis for: {$this->koperasi->nama_koperasi}");

        try {
            $aiAllocationService->analyze($this->koperasi);
            Log::info("Async AI fund allocation analysis complete for: {$this->koperasi->nama_koperasi}");
        } catch (\Exception $e) {
            Log::error("Async AI fund allocation analysis failed for: {$this->koperasi->nama_koperasi}", [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
