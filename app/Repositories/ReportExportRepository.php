<?php

namespace App\Repositories;

use App\Models\FamilyWelfareLog;
use App\Models\FinancialRecord;
use Illuminate\Support\Collection;

class ReportExportRepository
{
    /**
     * Get financial records filtered by date range.
     */
    public function getFinancialRecords(string $startDate, string $endDate): Collection
    {
        return FinancialRecord::with('koperasi')
            ->whereBetween('record_date', [$startDate, $endDate])
            ->orderBy('record_date', 'asc')
            ->get();
    }

    /**
     * Get family welfare logs filtered by date range.
     * Note: Assuming created_at is the filtering field as it's a log.
     */
    public function getSocialImpactLogs(string $startDate, string $endDate): Collection
    {
        return FamilyWelfareLog::whereBetween('created_at', [$startDate.' 00:00:00', $endDate.' 23:59:59'])
            ->with('user')
            ->latest()
            ->get();
    }
}
