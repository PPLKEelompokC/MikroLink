<?php

use App\Models\CommunityDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('guest is redirected from legal document upload page', function () {
    $this->get(route('docs.upload.form'))
        ->assertRedirect(route('login', absolute: false));
});

test('member can view legal document upload page', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get(route('docs.upload.form'))
        ->assertOk()
        ->assertSee('Validasi Dokumen Komunitas')
        ->assertSee('NIB')
        ->assertSee('Surat Keterangan');
});

test('member can submit a legal document for validation', function () {
    Storage::fake('local');

    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->post(route('docs.store'), [
            'document_name' => 'NIB',
            'file' => UploadedFile::fake()->create('nib-usaha.pdf', 256, 'application/pdf'),
            'note' => 'NIB usaha warung sembako.',
        ])
        ->assertRedirect(route('docs.upload.form', absolute: false))
        ->assertSessionHas('success');

    $document = CommunityDocument::firstOrFail();

    expect($document->user_id)->toBe($user->id)
        ->and($document->document_name)->toBe('NIB')
        ->and($document->status)->toBe('pending')
        ->and($document->note)->toBe('NIB usaha warung sembako.');

    Storage::disk('local')->assertExists($document->file_path);
});

test('member cannot submit unsupported document type', function () {
    Storage::fake('local');

    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->post(route('docs.store'), [
            'document_name' => 'Kartu Keluarga',
            'file' => UploadedFile::fake()->create('dokumen.txt', 10, 'text/plain'),
        ])
        ->assertSessionHasErrors(['document_name', 'file']);

    expect(CommunityDocument::count())->toBe(0);
});

test('admin can view pending legal documents', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $member = User::factory()->create(['name' => 'Anggota Legalitas', 'role' => 'user']);

    CommunityDocument::create([
        'user_id' => $member->id,
        'document_name' => 'Surat Keterangan',
        'file_path' => 'community-documents/surat.pdf',
        'status' => 'pending',
        'note' => 'Surat keterangan dari RT.',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.docs.index'))
        ->assertOk()
        ->assertSee('Verifikasi & Keanggotaan')
        ->assertSee('Pending KYC')
        ->assertSee('Anggota Legalitas')
        ->assertSee('Surat Keterangan');
});

test('admin can approve a legal document', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $member = User::factory()->create(['role' => 'user']);

    $document = CommunityDocument::create([
        'user_id' => $member->id,
        'document_name' => 'NIB',
        'file_path' => 'community-documents/nib.pdf',
        'status' => 'pending',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.docs.update', $document), [
            'status' => 'approved',
            'note' => 'Dokumen legalitas telah diverifikasi.',
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('community_documents', [
        'id' => $document->id,
        'status' => 'approved',
        'note' => 'Dokumen legalitas telah diverifikasi.',
    ]);
});

test('admin must provide a reason when rejecting a legal document', function () {
    $admin = User::factory()->create(['role' => 'Admin Koperasi']);
    $member = User::factory()->create(['role' => 'user']);

    $document = CommunityDocument::create([
        'user_id' => $member->id,
        'document_name' => 'NIB',
        'file_path' => 'community-documents/nib.pdf',
        'status' => 'pending',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.docs.update', $document), [
            'status' => 'rejected',
        ])
        ->assertSessionHasErrors(['note']);

    $this->assertDatabaseHas('community_documents', [
        'id' => $document->id,
        'status' => 'pending',
    ]);
});
