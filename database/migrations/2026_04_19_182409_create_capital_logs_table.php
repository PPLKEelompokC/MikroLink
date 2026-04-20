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
        Schema::create('capital_logs', function (Blueprint $table) {
            $table->id();
            $table->string('koperasi_id');
            $table->foreign('koperasi_id')->references('id_koperasi')->on('koperasi')->onDelete('cascade');
            $table->string('transaction_id')->unique();
            $table->enum('type', ['Pinjaman Usaha', 'Simpanan', 'Penyesuaian Modal', 'Dana Darurat']);
            $table->double('amount');
            $table->enum('status', ['Disetujui', 'Dalam Review', 'Ditolak', 'Selesai']);
            $table->integer('progress')->default(100);
            $table->string('member_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capital_logs');
    }
};
