<?php

namespace App\Http\Controllers;

use App\Models\Deposit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AktaSetoranController extends Controller
{
    public function download(int $id)
    {
        // Ambil data deposit beserta relasi user
        $deposit = Deposit::with('user')->findOrFail($id);

        // Pastikan hanya deposit yang APPROVED yang bisa didownload
        if ($deposit->status !== 'APPROVED') {
            abort(403, 'Akta hanya tersedia untuk setoran yang sudah disetujui.');
        }

        // Pastikan hanya pemilik atau admin yang bisa download
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['Admin Koperasi', 'Manajer Koperasi', 'admin']);
        $isOwner = $deposit->user_id === $user->id;

        if (!$isAdmin && !$isOwner) {
            abort(403, 'Anda tidak memiliki akses untuk mendownload akta ini.');
        }

        // Generate PDF dari template blade
        $pdf = Pdf::loadView('pdf.akta-setoran', compact('deposit'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'     => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'dpi'             => 150,
            ]);

        // Nama file PDF
        $filename = 'Akta-Setoran-' . str_pad($deposit->id, 5, '0', STR_PAD_LEFT) . '-' . $deposit->user->name . '.pdf';
        $filename = str_replace(' ', '-', $filename);

        return $pdf->download($filename);
    }
}