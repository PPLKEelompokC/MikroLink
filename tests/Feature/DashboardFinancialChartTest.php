<?php

use App\Models\Koperasi;
use App\Models\User;
use App\Models\NeracaKeuangan;

test('dashboard displays financial chart data for authenticated users', function () {
    $user = User::factory()->create(['role' => 'Admin Koperasi']);

    $koperasi = Koperasi::firstOrCreate(
        ['id_koperasi' => 'KOP-001'],
        ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
    );

    NeracaKeuangan::create([
        'koperasi_id' => $koperasi->id_koperasi,
        'periode' => now()->subMonth()->format('Y-m'),
        'periode_label' => now()->subMonth()->format('F Y'),
        'total_aset' => 25000000,
        'total_kewajiban' => 0,
        'modal_sendiri' => 0,
        'total_pendapatan' => 0,
        'total_biaya' => 0,
        'sisa_hasil_usaha' => 0,
    ]);

    NeracaKeuangan::create([
        'koperasi_id' => $koperasi->id_koperasi,
        'periode' => now()->format('Y-m'),
        'periode_label' => now()->format('F Y'),
        'total_aset' => 35000000,
        'total_kewajiban' => 0,
        'modal_sendiri' => 0,
        'total_pendapatan' => 0,
        'total_biaya' => 0,
        'sisa_hasil_usaha' => 0,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful();
    $response->assertViewHas('chartLabels');
    $response->assertViewHas('omzetData');
    $response->assertViewHas('creditScoreData');
    $response->assertViewHas('omzetPercentage');
    $response->assertViewHas('latestCreditScore');
    $response->assertSee('Kesehatan Finansial');
    $response->assertSee('Tren Pertumbuhan Omzet Harian');
    $response->assertSee('Kelola Pinjaman');
});

test('dashboard chart data is ordered by date ascending', function () {
    $user = User::factory()->create(['role' => 'Admin Koperasi']);

    $koperasi = Koperasi::firstOrCreate(
        ['id_koperasi' => 'KOP-001'],
        ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
    );

    // This data is newer but we create it first in DB to test ordering
    NeracaKeuangan::create([
        'koperasi_id' => $koperasi->id_koperasi,
        'periode' => now()->format('Y-m'),
        'periode_label' => now()->format('F Y'),
        'total_aset' => 50000000,
        'total_kewajiban' => 0,
        'modal_sendiri' => 0,
        'total_pendapatan' => 0,
        'total_biaya' => 0,
        'sisa_hasil_usaha' => 0,
    ]);

    // This data is older
    NeracaKeuangan::create([
        'koperasi_id' => $koperasi->id_koperasi,
        'periode' => now()->subMonths(3)->format('Y-m'),
        'periode_label' => now()->subMonths(3)->format('F Y'),
        'total_aset' => 10000000,
        'total_kewajiban' => 0,
        'modal_sendiri' => 0,
        'total_pendapatan' => 0,
        'total_biaya' => 0,
        'sisa_hasil_usaha' => 0,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful();

    $omzetData = $response->viewData('omzetData');

    // First entry should be the older (smaller) value because dashboard sorts ASC
    expect($omzetData[0])->toBeLessThan($omzetData[1]);
});

test('dashboard handles empty financial records gracefully', function () {
    $user = User::factory()->create(['role' => 'Admin Koperasi']);

    Koperasi::firstOrCreate(
        ['id_koperasi' => 'KOP-001'],
        ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
    );

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful();
    $response->assertViewHas('chartLabels', []);
    $response->assertViewHas('omzetData', []);
    $response->assertViewHas('creditScoreData', []);
});
