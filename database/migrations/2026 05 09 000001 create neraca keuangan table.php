<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('neraca_keuangan', function (Blueprint $table) {
            $table->id();
            $table->string('koperasi_id');
            $table->foreign('koperasi_id')->references('id_koperasi')->on('koperasi')->onDelete('cascade');

            $table->date('periode');                          // Tanggal laporan (akhir bulan)
            $table->string('periode_label');                  // e.g. "Mei 2026"

            // === ASET ===
            $table->decimal('kas',               15, 2)->default(0); // Saldo kas koperasi
            $table->decimal('simpanan_pokok',    15, 2)->default(0); // Total simpanan pokok anggota
            $table->decimal('simpanan_wajib',    15, 2)->default(0);
            $table->decimal('simpanan_sukarela', 15, 2)->default(0);
            $table->decimal('piutang_pinjaman',  15, 2)->default(0); // Total pinjaman aktif yg belum lunas
            $table->decimal('total_aset',        15, 2)->default(0); // kas + piutang + simpanan (aset yg dikuasai)

            // === KEWAJIBAN ===
            $table->decimal('kewajiban_tarik_pending', 15, 2)->default(0); // Penarikan pending
            $table->decimal('total_kewajiban',         15, 2)->default(0);

            // === EKUITAS / MODAL ===
            $table->decimal('modal_disetor',   15, 2)->default(0); // simpanan pokok + wajib
            $table->decimal('sisa_hasil_usaha',15, 2)->default(0); // kas - kewajiban - modal
            $table->decimal('total_ekuitas',   15, 2)->default(0); // modal + SHU

            // === RASIO KESEHATAN ===
            $table->decimal('rasio_likuiditas', 8, 2)->default(0); // kas / kewajiban * 100
            $table->decimal('rasio_kecukupan',  8, 2)->default(0); // ekuitas / total_aset * 100

            $table->boolean('is_auto')->default(true);  // true = digenerate otomatis
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['koperasi_id', 'periode']); // 1 laporan per bulan per koperasi
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('neraca_keuangan');
    }
};