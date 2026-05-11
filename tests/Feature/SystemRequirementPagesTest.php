<?php

use App\Models\FundAllocation;
use App\Models\IdleFundSnapshot;
use App\Models\Koperasi;
use App\Models\Loan;
use App\Models\TrustMetric;
use App\Models\User;

test('guests are redirected from system requirement pages', function () {
    $this->get('/kebutuhan-sistem/kelayakan')->assertRedirect('/login');
    $this->get('/kebutuhan-sistem/persetujuan')->assertRedirect('/login');
    $this->get('/kebutuhan-sistem/alokasi-dana')->assertRedirect('/login');
});

test('admin can view eligibility scoring page for fr 05', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $member = User::factory()->create(['name' => 'Anggota Skor', 'role' => 'user']);
    $this->actingAs($admin);

    TrustMetric::create([
        'user_id' => $member->id,
        'participation_score' => 80,
        'integrity_score' => 90,
        'reliability_score' => 70,
        'notes' => 'Riwayat pembayaran aktif.',
    ]);

    Loan::create([
        'user_id' => $member->id,
        'type' => 'Pinjaman Usaha',
        'amount' => 15000000,
        'duration' => 12,
        'reason' => 'Modal usaha warung',
        'status' => 'Baru',
        'progress_percentage' => 0,
    ]);

    $this->get('/kebutuhan-sistem/kelayakan')
        ->assertOk()
        ->assertSee('Indeks Kepercayaan & Kelayakan')
        ->assertSee('Sistem harus dapat menghitung dan menampilkan skor kelayakan pengajuan pinjaman menggunakan parameter riwayat partisipasi dan status kepatuhan pembayaran dari basis data.')
        ->assertSee('Anggota Skor')
        ->assertSee('Riwayat pembayaran aktif.');
});

test('manager can view approval workflow page for fr 06', function () {
    $manager = User::factory()->create(['role' => 'Manajer Koperasi']);
    $member = User::factory()->create(['name' => 'Anggota Workflow', 'role' => 'user']);
    $this->actingAs($manager);

    Loan::create([
        'user_id' => $member->id,
        'type' => 'Pinjaman alat produksi',
        'amount' => 25000000,
        'duration' => 18,
        'reason' => 'Pengajuan untuk alat produksi.',
        'status' => 'Baru',
        'progress_percentage' => 0,
    ]);

    $this->get('/kebutuhan-sistem/persetujuan')
        ->assertOk()
        ->assertSee('Workflow Persetujuan Berjenjang')
        ->assertSee('Sistem harus merutekan status pengajuan pinjaman secara berurutan dan mengunci fungsi pencairan hingga mendapatkan persetujuan dari akun Admin dan Manajer.')
        ->assertSee('Pencairan')
        ->assertSee('Terkunci');
});

test('manager can view strategic allocation page for fr 18', function () {
    $manager = User::factory()->create(['role' => 'Manajer Koperasi']);
    $this->actingAs($manager);

    $koperasi = Koperasi::create([
        'id_koperasi' => 'KOP-001',
        'nama_koperasi' => 'Koperasi MikroLink',
        'alamat' => 'Jl. Merdeka No 1',
        'saldo_kas' => 400000000,
        'total_outstanding_loans' => 50000000,
    ]);

    $snapshot = IdleFundSnapshot::create([
        'koperasi_id' => $koperasi->id_koperasi,
        'snapshot_date' => now()->toDateString(),
        'total_cash_balance' => 400000000,
        'total_outstanding_loans' => 50000000,
        'total_pending_deposits' => 0,
        'operational_reserve' => 100000000,
        'idle_fund_amount' => 250000000,
    ]);

    FundAllocation::create([
        'koperasi_id' => $koperasi->id_koperasi,
        'snapshot_id' => $snapshot->id,
        'recommended_amount' => 120000000,
        'allocation_category' => 'Modal produktif UMKM',
        'confidence_score' => 88,
        'reasoning' => 'Dana mengendap tersedia untuk alokasi strategis.',
        'ai_model_used' => 'openrouter',
        'status' => 'pending',
    ]);

    $this->get('/kebutuhan-sistem/alokasi-dana')
        ->assertOk()
        ->assertSee('AI Alokasi Dana Strategis')
        ->assertSee('Sistem harus memproses data analitik ketersediaan dana mengendap (idle) dan memberikan notifikasi rekomendasi nominal alokasi dana strategis pada dashboard Manajer.')
        ->assertSee('Modal produktif UMKM')
        ->assertSee('Dana mengendap tersedia untuk alokasi strategis.');
});
