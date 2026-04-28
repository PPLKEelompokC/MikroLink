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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id_number')->unique(); // Format: PN-YYYY-XXX
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // Pinjaman Usaha / Pinjaman Konsumsi / Pinjaman Darurat
            $table->decimal('amount', 15, 2);
            $table->unsignedSmallInteger('duration'); // Tenor in months
            $table->text('reason');
            $table->enum('status', ['Baru', 'Dalam Review', 'Disetujui', 'Ditolak'])->default('Baru');
            $table->unsignedTinyInteger('progress_percentage')->default(0); // 0-100, computed from stages
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
