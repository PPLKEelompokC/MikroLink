<?php

namespace App\Console\Commands;

use App\Models\Koperasi;
use App\Services\AiAllocationService;
use Illuminate\Console\Command;

class AnalyzeIdleFundsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fund-allocation:analyze
                            {--koperasi= : Specific koperasi ID to analyze (default: all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Analyze idle funds and generate AI-driven strategic allocation recommendations';

    /**
     * Execute the console command.
     */
    public function handle(AiAllocationService $aiAllocationService): int
    {
        $koperasiId = $this->option('koperasi');

        $koperasiQuery = Koperasi::query();

        if ($koperasiId) {
            $koperasiQuery->where('id_koperasi', $koperasiId);
        }

        $koperasiList = $koperasiQuery->get();

        if ($koperasiList->isEmpty()) {
            $this->error('No koperasi found to analyze.');

            return self::FAILURE;
        }

        foreach ($koperasiList as $koperasi) {
            $this->info("Analyzing idle funds for: {$koperasi->nama_koperasi} ({$koperasi->id_koperasi})");

            try {
                $allocations = $aiAllocationService->analyze($koperasi);

                $this->info(sprintf(
                    '  ✓ Generated %d allocation recommendations.',
                    $allocations->count()
                ));

                foreach ($allocations as $allocation) {
                    $this->line(sprintf(
                        '    - %s: Rp %s (confidence: %s%%)',
                        $allocation->allocation_category,
                        number_format($allocation->recommended_amount, 0, ',', '.'),
                        $allocation->confidence_score
                    ));
                }
            } catch (\Exception $exception) {
                $this->error("  ✗ Failed: {$exception->getMessage()}");
            }
        }

        $this->newLine();
        $this->info('Fund allocation analysis complete.');

        return self::SUCCESS;
    }
}
