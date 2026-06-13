<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class FinancialReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    public function __construct(private $data) {}

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function headings(): array
    {
        return [
            'ID Koperasi',
            'Nama Koperasi',
            'Periode',
            'Total Aset (IDR)',
            'Total Ekuitas (IDR)',
            'Status Kesehatan',
        ];
    }

    public function map($record): array
    {
        return [
            $record->koperasi_id,
            $record->koperasi->nama_koperasi ?? '-',
            $record->periode_label,
            number_format($record->total_aset, 0, ',', '.'),
            number_format($record->total_ekuitas, 0, ',', '.'),
            $record->statusKesehatan(),
        ];
    }
}
