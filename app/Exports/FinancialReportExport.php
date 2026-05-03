<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class FinancialReportExport implements FromCollection, WithHeadings, WithTitle, WithMapping, ShouldAutoSize
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
            'Tanggal Catatan',
            'Omzet (IDR)',
            'Skor Kredit',
        ];
    }

    public function map($record): array
    {
        return [
            $record->koperasi_id,
            $record->koperasi->nama_koperasi ?? '-',
            $record->record_date->format('d/m/Y'),
            number_format($record->omzet, 0, ',', '.'),
            $record->credit_score,
        ];
    }
}
