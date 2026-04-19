<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('community_documents', function (Blueprint $table) {
            $table->id();
            // Menghubungkan dokumen ke tabel communities
            // onDelete('cascade') artinya jika data komunitas dihapus, dokumennya ikut terhapus
            $table->foreignId('community_id')->constrained()->onDelete('cascade');
            
            $table->string('document_name'); // Nama dokumen (misal: "KTP Pengurus")
            $table->string('file_path');     // Lokasi file di folder storage
            
            // Status validasi: pending (menunggu), approved (disetujui), rejected (ditolak)
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            $table->text('note')->nullable(); // Catatan admin jika dokumen ditolak
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_documents');
    }
};