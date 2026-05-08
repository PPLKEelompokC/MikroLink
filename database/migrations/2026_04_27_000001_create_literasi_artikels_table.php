<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('literasi_artikels', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('ringkasan');
            $table->longText('konten');
            $table->string('kategori');
            $table->string('thumbnail')->nullable();
            $table->integer('estimasi_baca')->default(5);
            $table->enum('level', ['pemula', 'menengah', 'mahir'])->default('pemula');
            $table->boolean('is_published')->default(true);
            $table->integer('views')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('literasi_artikels');
    }
};