<?php

use App\Models\Loan;
use App\Models\LoanStage;
use App\Models\User;
use Livewire\Volt\Volt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the disbursement tracking component for admin', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    
    $this->actingAs($admin)
        ->get(route('admin.pinjaman.validasi'))
        ->assertStatus(200)
        ->assertSee('Progress Tracking Operasi');
});

it('lists loans correctly', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);
    $loan = Loan::factory()->create(['user_id' => $user->id, 'loan_id_number' => 'LN-123']);
    
    Volt::actingAs($admin)
        ->test('admin.disbursement-tracking')
        ->assertSee('LN-123')
        ->assertSee($user->name);
});

it('can filter loans by status', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);
    $loan1 = Loan::factory()->create(['user_id' => $user->id, 'status' => 'Baru', 'loan_id_number' => 'LN-BARU']);
    $loan2 = Loan::factory()->create(['user_id' => $user->id, 'status' => 'Disetujui', 'loan_id_number' => 'LN-SETUJU']);
    
    Volt::actingAs($admin)
        ->test('admin.disbursement-tracking')
        ->set('filterStatus', 'Baru')
        ->assertSee('LN-BARU')
        ->assertDontSee('LN-SETUJU');
});

it('can view loan details and advance a loan stage', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);
    $loan = Loan::factory()->create(['user_id' => $user->id, 'status' => 'Baru', 'loan_id_number' => 'LN-123']);
    
    // Create stages
    LoanStage::create([
        'loan_id' => $loan->id,
        'stage_order' => 1,
        'stage_name' => 'Verifikasi Berkas',
        'completed' => false,
    ]);
    LoanStage::create([
        'loan_id' => $loan->id,
        'stage_order' => 2,
        'stage_name' => 'Survey Lapangan',
        'completed' => false,
    ]);

    Volt::actingAs($admin)
        ->test('admin.disbursement-tracking')
        ->call('lihatDetail', $loan->id)
        ->assertSee('Detail Pinjaman')
        ->assertSee('LN-123')
        ->call('advanceStage', $loan->id)
        ->assertDispatched('notif'); // We can't assert the exact message easily if it varies, but checking the event works

    $this->assertDatabaseHas('loan_stages', [
        'loan_id' => $loan->id,
        'stage_order' => 1,
        'completed' => true,
        'completed_by_id' => $admin->id,
    ]);

    // Recalculate should have been called
    $loan->refresh();
    expect($loan->progress_percentage)->toBe(50);
    expect($loan->status)->toBe('Dalam Review');
});

it('can reject a loan', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $user = User::factory()->create(['role' => 'user']);
    $loan = Loan::factory()->create(['user_id' => $user->id, 'status' => 'Baru']);

    Volt::actingAs($admin)
        ->test('admin.disbursement-tracking')
        ->set('stageNote', 'Berkas tidak valid')
        ->call('rejectLoan', $loan->id)
        ->assertDispatched('notif');

    $this->assertDatabaseHas('loans', [
        'id' => $loan->id,
        'status' => 'Ditolak',
        'notes' => 'Berkas tidak valid',
    ]);
});
