<?php

use App\Models\Koperasi;
use App\Models\User;
use App\Services\KoperasiCapitalService;
use App\Services\NeracaKeuanganService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->service = new KoperasiCapitalService();
    $this->koperasi = Koperasi::create([
        'id_koperasi' => 'KOP-001',
        'nama_koperasi' => 'Koperasi Sejahtera',
        'alamat' => 'Jl. Merdeka',
        'saldo_kas' => 0,
    ]);
    $this->user = User::factory()->create();
});

it('can process deposit', function () {
    $log = $this->service->processCapitalTransaction(
        $this->koperasi,
        $this->user->id,
        100000,
        'simpanan_pokok',
        'deposit'
    );

    expect($log->amount)->toBe(100000.0);
    expect($log->type)->toBe('simpanan_pokok');
    expect($log->transaction_type)->toBe('deposit');
    expect($this->koperasi->fresh()->saldo_kas)->toBe(100000.0);
});

it('cannot withdraw simpanan_wajib', function () {
    $this->service->processCapitalTransaction(
        $this->koperasi,
        $this->user->id,
        50000,
        'simpanan_wajib',
        'withdrawal'
    );
})->throws(\Exception::class, 'Cannot withdraw simpanan_wajib');

it('can withdraw simpanan_sukarela with sufficient balance', function () {
    // Deposit first
    $this->service->processCapitalTransaction(
        $this->koperasi,
        $this->user->id,
        200000,
        'simpanan_sukarela',
        'deposit'
    );

    // Withdraw
    $log = $this->service->processCapitalTransaction(
        $this->koperasi,
        $this->user->id,
        50000,
        'simpanan_sukarela',
        'withdrawal'
    );

    expect($log->amount)->toBe(50000.0);
    expect($this->service->getAvailableSukarelaBalance($this->koperasi->id_koperasi, $this->user->id))->toBe(150000.0);
});

it('cannot withdraw simpanan_sukarela with insufficient balance', function () {
    // Deposit 50k
    $this->service->processCapitalTransaction(
        $this->koperasi,
        $this->user->id,
        50000,
        'simpanan_sukarela',
        'deposit'
    );

    // Try withdrawing 100k
    $this->service->processCapitalTransaction(
        $this->koperasi,
        $this->user->id,
        100000,
        'simpanan_sukarela',
        'withdrawal'
    );
})->throws(\Exception::class, 'Insufficient simpanan_sukarela balance.');

it('neraca keuangan service correctly aggregates capital logs', function () {
    $neracaService = new NeracaKeuanganService();

    // Deposit Pokok
    $this->service->processCapitalTransaction($this->koperasi, $this->user->id, 100000, 'simpanan_pokok', 'deposit');
    // Deposit Wajib
    $this->service->processCapitalTransaction($this->koperasi, $this->user->id, 50000, 'simpanan_wajib', 'deposit');
    // Deposit Sukarela
    $this->service->processCapitalTransaction($this->koperasi, $this->user->id, 200000, 'simpanan_sukarela', 'deposit');
    // Withdraw Sukarela
    $this->service->processCapitalTransaction($this->koperasi, $this->user->id, 50000, 'simpanan_sukarela', 'withdrawal');

    $neraca = $neracaService->generate($this->koperasi, now());

    expect((float)$neraca->simpanan_pokok)->toBe(100000.0);
    expect((float)$neraca->simpanan_wajib)->toBe(50000.0);
    expect((float)$neraca->simpanan_sukarela)->toBe(150000.0); // 200k - 50k
    expect((float)$neraca->kas)->toBe(300000.0); // Total kas = 100k + 50k + 200k - 50k
});
