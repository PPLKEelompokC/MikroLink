<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SocialImpactExport implements FromCollection, WithHeadings, WithTitle, WithMapping, ShouldAutoSize
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
            'Informasi Dampak',
            'Metadata',
        ];
    }

    public function map($log): array
    {
        return [
            $log->id,
            $log->created_at->format('d/m/Y H:i'),
            'Tracking Kesejahteraan Keluarga',
            'Target SDG 1 - Tanpa Kemiskinan',
        ];
    }
}
