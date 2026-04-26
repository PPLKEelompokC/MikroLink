<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KoperasiController extends Controller
{
    public function edit()
    {
        $koperasi = \App\Models\Koperasi::firstOrCreate(
            ['id_koperasi' => 'KOP-001'],
            ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
        );

        return view('koperasi.edit', compact('koperasi'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_koperasi' => 'required|string|max:255',
            'alamat' => 'required|string',
        ]);

        $koperasi = \App\Models\Koperasi::first();
        if ($koperasi) {
            $koperasi->update($request->only(['nama_koperasi', 'alamat']));
        }

        return redirect()->route('koperasi.edit')->with('success', 'Profil Koperasi berhasil diperbarui.');
    }

    public function adjustCapital(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $koperasi = \App\Models\Koperasi::first();
        if ($koperasi) {
            $koperasi->updateSaldo((float) $request->amount);
        }

        return redirect()->route('koperasi.edit')->with('success', 'Saldo Kas berhasil disesuaikan.');
    }
}
