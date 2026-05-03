<?php

namespace App\Services;

use App\Repositories\ReportExportRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CombinedReportExport;

class MultiFormatExportService
{
    public function __construct(
        private ReportExportRepository $repository
    ) {}

    /**
     * Generate the export file.
     */
    public function export(string $startDate, string $endDate, string $format)
    {
        $financialData = $this->repository->getFinancialRecords($startDate, $endDate);
        $socialImpactData = $this->repository->getSocialImpactLogs($startDate, $endDate);

        $filename = 'Laporan_MikroLink_' . now()->format('YmdHis');
        $exportClass = new CombinedReportExport($financialData, $socialImpactData);

        if ($format === 'csv') {
            return Excel::download($exportClass, $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download($exportClass, $filename . '.xlsx', \Maatwebsite\Excel\Excel::XLSX);
    }
}
