<?php

use App\Models\FinancialRecord;
use App\Models\Koperasi;
use App\Models\User;

test('dashboard displays financial chart data for authenticated users', function () {
    $user = User::factory()->create(['role' => 'Admin Koperasi']);

    $koperasi = Koperasi::firstOrCreate(
        ['id_koperasi' => 'KOP-001'],
        ['nama_koperasi' => 'Koperasi MikroLink', 'alamat' => 'Jl. Merdeka No 1', 'saldo_kas' => 350500000]
    );

    FinancialRecord::factory()->create([
        'koperasi_id' => $koperasi->id_koperasi,
        'record_date' => now()->subMonth(),
        'omzet' => 25000000,
        'credit_score' => 8.5,
    ]);

    FinancialRecord::factory()->create([
        'koperasi_id' => $koperasi->id_koperasi,
        'record_date' => now(),
        'omzet' => 35000000,
        'credit_score' => 11.0,
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

    FinancialRecord::factory()->create([
        'koperasi_id' => $koperasi->id_koperasi,
        'record_date' => now(),
        'omzet' => 50000000,
        'credit_score' => 12.0,
    ]);

    FinancialRecord::factory()->create([
        'koperasi_id' => $koperasi->id_koperasi,
        'record_date' => now()->subMonths(3),
        'omzet' => 10000000,
        'credit_score' => 5.0,
    ]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertSuccessful();

    $omzetData = $response->viewData('omzetData');

    // First entry should be the older (smaller) value
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
