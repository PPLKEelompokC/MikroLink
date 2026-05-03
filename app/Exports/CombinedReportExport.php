<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CombinedReportExport implements WithMultipleSheets
{
    public function __construct(
        private $financialData,
        private $socialImpactData
    ) {}

    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            new FinancialReportExport($this->financialData),
            new SocialImpactExport($this->socialImpactData),
        ];
    }
}
