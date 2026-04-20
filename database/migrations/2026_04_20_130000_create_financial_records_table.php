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
        Schema::create('financial_records', function (Blueprint $table) {
            $table->id();
            $table->string('koperasi_id');
            $table->foreign('koperasi_id')->references('id_koperasi')->on('koperasi')->onDelete('cascade');
            $table->date('record_date')->index();
            $table->double('omzet')->default(0);
            $table->double('credit_score')->default(0);
            $table->timestamps();

            $table->unique(['koperasi_id', 'record_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_records');
    }
};
