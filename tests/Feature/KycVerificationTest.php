<?php

use App\Models\KycVerification;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

test('guest is redirected or blocked from KYC endpoints', function () {
    $this->postJson(route('kyc.process'))
        ->assertStatus(401);

    $this->postJson(route('kyc.submit'))
        ->assertStatus(401);
});

test('member can process KTP with mock successful OCR response', function () {
    Storage::fake('local');
    Http::fake([
        '*api.ocr.space/parse/image*' => Http::response([
            'ParsedResults' => [
                [
                    'ParsedText' => "NIK: 3273 0123 4567 8901\nNama: Daffa",
                ],
            ],
        ], 200),
    ]);

    $user = User::factory()->create(['role' => 'user']);
    $file = UploadedFile::fake()->create('ktp.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($user)
        ->postJson(route('kyc.process'), [
            'ktp_photo' => $file,
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'nik' => '3273012345678901',
        ]);

    $data = $response->json();
    Storage::disk('local')->assertExists($data['ktp_path']);
});

test('OCR failure/timeout does not trigger global error', function () {
    Storage::fake('local');
    Http::fake([
        '*api.ocr.space/parse/image*' => Http::response(null, 500),
    ]);

    $user = User::factory()->create(['role' => 'user']);
    $file = UploadedFile::fake()->create('ktp.jpg', 100, 'image/jpeg');

    $response = $this->actingAs($user)
        ->postJson(route('kyc.process'), [
            'ktp_photo' => $file,
        ]);

    $response->assertStatus(500)
        ->assertJson([
            'success' => false,
            'message' => 'Gagal memproses OCR KTP.',
        ]);
});

test('member can submit KYC after OCR processes successfully', function () {
    $user = User::factory()->create(['role' => 'user']);

    $response = $this->actingAs($user)
        ->postJson(route('kyc.submit'), [
            'nik' => '3273012345678901',
            'ktp_path' => 'private/kyc/mock-ktp.jpg',
        ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('kyc_verifications', [
        'user_id' => $user->id,
        'nik' => '3273012345678901',
        'status' => 'PENDING',
    ]);
});

test('admin can approve KYC verification', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $member = User::factory()->create(['role' => 'user']);

    $verification = KycVerification::create([
        'user_id' => $member->id,
        'ktp_path' => 'private/kyc/ktp.jpg',
        'nik' => '3273012345678901',
        'status' => 'PENDING',
    ]);

    $response = $this->actingAs($admin)
        ->patch(route('admin.kyc.approve', $verification->id));

    $response->assertRedirect();

    $this->assertDatabaseHas('kyc_verifications', [
        'id' => $verification->id,
        'status' => 'APPROVED',
    ]);
});

test('admin must supply rejection reason to reject KYC verification', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $member = User::factory()->create(['role' => 'user']);

    $verification = KycVerification::create([
        'user_id' => $member->id,
        'ktp_path' => 'private/kyc/ktp.jpg',
        'nik' => '3273012345678901',
        'status' => 'PENDING',
    ]);

    // Rejection without reason fails validation
    $response = $this->actingAs($admin)
        ->patch(route('admin.kyc.reject', $verification->id), [
            'rejection_reason' => '',
        ]);

    $response->assertSessionHasErrors(['rejection_reason']);

    // Rejection with reason succeeds
    $response = $this->actingAs($admin)
        ->patch(route('admin.kyc.reject', $verification->id), [
            'rejection_reason' => 'Foto KTP buram.',
        ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('kyc_verifications', [
        'id' => $verification->id,
        'status' => 'REJECTED',
        'rejection_reason' => 'Foto KTP buram.',
    ]);
});
