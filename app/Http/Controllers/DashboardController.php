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

        return view('dashboard', compact('koperasi', 'availableCapital', 'likuiditas', 'totalTransaksi', 'terakhirDiperbarui', 'capitalLogs'));
    }
}
