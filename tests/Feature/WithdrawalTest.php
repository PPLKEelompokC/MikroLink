<?php

use App\Models\Deposit;
use App\Models\User;
use App\Models\Withdrawal;
use App\Notifications\WithdrawalStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

// ── Member Tests ──────────────────────────────────────────────────

it('renders the withdrawal page for authenticated user', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('simpanan.tarik'))
        ->assertSuccessful()
        ->assertSee('Formulir Penarikan Simpanan');
});

it('displays available sukarela balance correctly', function () {
    $user = User::factory()->create(['role' => 'user']);

    Deposit::create([
        'user_id' => $user->id,
        'amount' => 500000,
        'type' => 'SUKARELA',
        'proof_path' => 'test.jpg',
        'status' => 'APPROVED',
    ]);

    Volt::actingAs($user)
        ->test('simpanan.tarik-simpanan')
        ->assertSee('Rp 500.000');
});

it('can submit a withdrawal request', function () {
    $user = User::factory()->create(['role' => 'user']);

    Deposit::create([
        'user_id' => $user->id,
        'amount' => 500000,
        'type' => 'SUKARELA',
        'proof_path' => 'test.jpg',
        'status' => 'APPROVED',
    ]);

    Volt::actingAs($user)
        ->test('simpanan.tarik-simpanan')
        ->set('amount', 100000)
        ->set('bank_name', 'BCA')
        ->set('bank_account', '1234567890')
        ->set('bank_account_name', 'BUDI SANTOSO')
        ->call('submit')
        ->assertDispatched('penarikan-berhasil');

    $this->assertDatabaseHas('withdrawals', [
        'user_id' => $user->id,
        'amount' => 100000,
        'bank_name' => 'BCA',
        'bank_account' => '1234567890',
        'status' => 'PENDING',
    ]);
});

it('prevents withdrawal exceeding available balance', function () {
    $user = User::factory()->create(['role' => 'user']);

    Deposit::create([
        'user_id' => $user->id,
        'amount' => 100000,
        'type' => 'SUKARELA',
        'proof_path' => 'test.jpg',
        'status' => 'APPROVED',
    ]);

    Volt::actingAs($user)
        ->test('simpanan.tarik-simpanan')
        ->set('amount', 200000)
        ->set('bank_name', 'BCA')
        ->set('bank_account', '1234567890')
        ->set('bank_account_name', 'BUDI SANTOSO')
        ->call('submit')
        ->assertHasErrors(['amount']);

    $this->assertDatabaseMissing('withdrawals', [
        'user_id' => $user->id,
    ]);
});

it('accounts for pending withdrawals in available balance', function () {
    $user = User::factory()->create(['role' => 'user']);

    Deposit::create([
        'user_id' => $user->id,
        'amount' => 200000,
        'type' => 'SUKARELA',
        'proof_path' => 'test.jpg',
        'status' => 'APPROVED',
    ]);

    Withdrawal::factory()->create([
        'user_id' => $user->id,
        'amount' => 150000,
        'status' => 'PENDING',
    ]);

    // Available = 200000 - 150000 = 50000, trying 100000 should fail
    Volt::actingAs($user)
        ->test('simpanan.tarik-simpanan')
        ->set('amount', 100000)
        ->set('bank_name', 'BCA')
        ->set('bank_account', '1234567890')
        ->set('bank_account_name', 'BUDI SANTOSO')
        ->call('submit')
        ->assertHasErrors(['amount']);
});

it('shows withdrawal history for the member', function () {
    $user = User::factory()->create(['role' => 'user']);

    Withdrawal::factory()->create([
        'user_id' => $user->id,
        'amount' => 75000,
        'bank_name' => 'BNI',
    ]);

    Volt::actingAs($user)
        ->test('simpanan.tarik-simpanan')
        ->assertSee('Rp 75.000')
        ->assertSee('BNI');
});

// ── Admin Tests ───────────────────────────────────────────────────

it('renders the admin withdrawal validation page', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);

    $this->actingAs($admin)
        ->get(route('admin.simpanan.tarik.validasi'))
        ->assertSuccessful()
        ->assertSee('Validasi Penarikan Simpanan');
});

it('lists pending withdrawals for admin', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);

    Withdrawal::factory()->create([
        'user_id' => $user->id,
        'amount' => 250000,
        'bank_name' => 'Mandiri',
    ]);

    Volt::actingAs($admin)
        ->test('admin.validasi-penarikan')
        ->assertSee('Rp 250.000')
        ->assertSee($user->name)
        ->assertSee('Mandiri');
});

it('admin can approve withdrawal with proof upload', function () {
    Storage::fake('public');
    Notification::fake();

    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);

    $withdrawal = Withdrawal::factory()->create([
        'user_id' => $user->id,
        'amount' => 100000,
    ]);

    Volt::actingAs($admin)
        ->test('admin.validasi-penarikan')
        ->call('lihatDetail', $withdrawal->id)
        ->set('proofFile', UploadedFile::fake()->image('bukti.jpg'))
        ->set('adminNote', 'Dana sudah ditransfer')
        ->call('approve', $withdrawal->id)
        ->assertDispatched('notif');

    $this->assertDatabaseHas('withdrawals', [
        'id' => $withdrawal->id,
        'status' => 'APPROVED',
        'admin_note' => 'Dana sudah ditransfer',
    ]);

    Notification::assertSentTo($user, WithdrawalStatusUpdated::class);
});

it('admin cannot approve without proof upload', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);

    $withdrawal = Withdrawal::factory()->create([
        'user_id' => $user->id,
    ]);

    Volt::actingAs($admin)
        ->test('admin.validasi-penarikan')
        ->call('lihatDetail', $withdrawal->id)
        ->call('approve', $withdrawal->id)
        ->assertHasErrors(['proofFile']);

    $this->assertDatabaseHas('withdrawals', [
        'id' => $withdrawal->id,
        'status' => 'PENDING',
    ]);
});

it('admin can reject withdrawal with reason', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);

    $withdrawal = Withdrawal::factory()->create([
        'user_id' => $user->id,
    ]);

    Volt::actingAs($admin)
        ->test('admin.validasi-penarikan')
        ->call('lihatDetail', $withdrawal->id)
        ->set('adminNote', 'Saldo koperasi tidak mencukupi saat ini')
        ->call('reject', $withdrawal->id)
        ->assertDispatched('notif');

    $this->assertDatabaseHas('withdrawals', [
        'id' => $withdrawal->id,
        'status' => 'REJECTED',
        'admin_note' => 'Saldo koperasi tidak mencukupi saat ini',
    ]);

    Notification::assertSentTo($user, WithdrawalStatusUpdated::class);
});

it('admin cannot reject without reason', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);

    $withdrawal = Withdrawal::factory()->create([
        'user_id' => $user->id,
    ]);

    Volt::actingAs($admin)
        ->test('admin.validasi-penarikan')
        ->call('lihatDetail', $withdrawal->id)
        ->set('adminNote', '')
        ->call('reject', $withdrawal->id)
        ->assertHasErrors(['adminNote']);

    $this->assertDatabaseHas('withdrawals', [
        'id' => $withdrawal->id,
        'status' => 'PENDING',
    ]);
});

// ── Security Tests ────────────────────────────────────────────────

it('regular user cannot access admin withdrawal validation page', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('admin.simpanan.tarik.validasi'))
        ->assertForbidden();
});

it('guest cannot access withdrawal pages', function () {
    $this->get(route('simpanan.tarik'))
        ->assertRedirect(route('login'));

    $this->get(route('admin.simpanan.tarik.validasi'))
        ->assertRedirect(route('login'));
});
