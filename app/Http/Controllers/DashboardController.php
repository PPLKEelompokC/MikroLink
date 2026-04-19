<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $koperasi = \App\Models\Koperasi::with('capitalLogs')->firstOrCreate(
            ['id_koperasi' => 'KOP-001'],
            ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
        );

        $availableCapital = $koperasi->saldo_kas;
        $likuiditas = $koperasi->cekLikuiditas();
        $totalTransaksi = $koperasi->capitalLogs->count();
        $terakhirDiperbarui = $koperasi->capitalLogs->last() ? $koperasi->capitalLogs->last()->created_at->diffForHumans() : 'Belum ada transaksi';
        $capitalLogs = $koperasi->capitalLogs()->latest()->take(5)->get();

        // AI Recommendation Skeleton
        $aiRecommendations = [
            'Mikro Usaha' => ['percentage' => 80, 'color' => 'bg-[#e8a838]'],
            'Pertanian' => ['percentage' => 70, 'color' => 'bg-blue-600'],
            'Pendidikan' => ['percentage' => 45, 'color' => 'bg-emerald-500'],
            'Darurat' => ['percentage' => 25, 'color' => 'bg-red-600'],
        ];
        $aiInsight = 'Tingkatkan alokasi ke sektor Mikro Usaha untuk ROI optimal (+2.1%)';

        return view('dashboard', compact('koperasi', 'availableCapital', 'likuiditas', 'totalTransaksi', 'terakhirDiperbarui', 'capitalLogs', 'aiRecommendations', 'aiInsight'));
    }
}
