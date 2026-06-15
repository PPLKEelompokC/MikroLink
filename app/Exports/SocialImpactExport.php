<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SocialImpactExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    public function __construct(private $data) {}

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Dampak Sosial (SDG 1)';
    }

    public function headings(): array
    {
        return [
            'ID Log',
            'Tanggal',
            'Anggota',
            'Periode',
            'Pendapatan Sebelum',
            'Pendapatan Sesudah',
            'Pertumbuhan Pendapatan',
            'Tanggungan',
            'Pangan',
            'Pendidikan',
            'Kesehatan',
            'Skor Kesejahteraan',
            'Catatan',
        ];
    }

    public function map($log): array
    {
        $userName = $log->user?->name ?? '-';
        $nik = $log->user?->kycVerification?->nik;

        if ($nik && strlen($nik) >= 16) {
            $maskedNik = substr($nik, 0, 4).str_repeat('*', 8).substr($nik, 12);
            $userName .= " ({$maskedNik})";
        }

        return [
            $log->id,
            $log->created_at->format('d/m/Y H:i'),
            $userName,
            $log->period_date?->format('d/m/Y'),
            $log->income_before,
            $log->income_after,
            $log->incomeGrowthPercentage().'%',
            $log->dependents_count,
            $log->food_security_status,
            $log->education_access_status,
            $log->health_access_status,
            $log->welfare_score,
            $log->notes,
        ];
    }
}
