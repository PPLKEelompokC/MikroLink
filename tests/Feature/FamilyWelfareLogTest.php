<?php

use App\Models\FamilyWelfareLog;
use App\Models\User;

test('guest is redirected from welfare pages', function () {
    $this->get(route('welfare.dashboard'))->assertRedirect(route('login', absolute: false));
    $this->get(route('welfare.create'))->assertRedirect(route('login', absolute: false));
});

test('member can view welfare dashboard and questionnaire form', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('welfare.dashboard'))
        ->assertOk()
        ->assertSee('Jejak Kesejahteraan Keluarga')
        ->assertSee('Isi Kuesioner');

    $this->actingAs($user)
        ->get(route('welfare.create'))
        ->assertOk()
        ->assertSee('Kuesioner Kesejahteraan Keluarga')
        ->assertSee('Pendapatan Sebelum Bantuan');
});

test('member can submit family welfare questionnaire', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->post(route('welfare.store'), [
            'period_date' => now()->toDateString(),
            'income_before' => 1500000,
            'income_after' => 2100000,
            'dependents_count' => 3,
            'food_security_status' => 'tercukupi',
            'education_access_status' => 'baik',
            'health_access_status' => 'terbatas',
            'notes' => 'Pendapatan naik setelah modal dipakai untuk stok harian.',
        ])
        ->assertRedirect(route('welfare.dashboard', absolute: false))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('family_welfare_logs', [
        'user_id' => $user->id,
        'income_before' => 1500000,
        'income_after' => 2100000,
        'dependents_count' => 3,
        'food_security_status' => 'tercukupi',
        'education_access_status' => 'baik',
        'health_access_status' => 'terbatas',
    ]);

    expect(FamilyWelfareLog::first()->welfare_score)->toBeGreaterThan(0);
});

test('member must submit valid family welfare questionnaire data', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->post(route('welfare.store'), [
            'period_date' => now()->addDay()->toDateString(),
            'income_before' => -1000,
            'income_after' => -1000,
            'dependents_count' => 40,
            'food_security_status' => 'unknown',
            'education_access_status' => 'unknown',
            'health_access_status' => 'unknown',
        ])
        ->assertSessionHasErrors([
            'period_date',
            'income_before',
            'income_after',
            'dependents_count',
            'food_security_status',
            'education_access_status',
            'health_access_status',
        ]);

    expect(FamilyWelfareLog::count())->toBe(0);
});

test('welfare dashboard only shows authenticated members own logs', function () {
    $user = User::factory()->create(['role' => 'user']);
    $otherUser = User::factory()->create(['role' => 'user']);

    FamilyWelfareLog::create([
        'user_id' => $user->id,
        'period_date' => now()->toDateString(),
        'income_before' => 1000000,
        'income_after' => 1300000,
        'dependents_count' => 2,
        'food_security_status' => 'rentan',
        'education_access_status' => 'terbatas',
        'health_access_status' => 'baik',
        'welfare_score' => 78,
        'notes' => 'Catatan milik user.',
    ]);

    FamilyWelfareLog::create([
        'user_id' => $otherUser->id,
        'period_date' => now()->toDateString(),
        'income_before' => 1000000,
        'income_after' => 1200000,
        'dependents_count' => 1,
        'food_security_status' => 'tercukupi',
        'education_access_status' => 'baik',
        'health_access_status' => 'baik',
        'welfare_score' => 90,
        'notes' => 'Catatan user lain.',
    ]);

    $this->actingAs($user)
        ->get(route('welfare.dashboard'))
        ->assertOk()
        ->assertSee('Catatan milik user.')
        ->assertDontSee('Catatan user lain.');
});
