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
        Schema::create('idle_fund_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('koperasi_id');
            $table->foreign('koperasi_id')->references('id_koperasi')->on('koperasi')->onDelete('cascade');
            $table->date('snapshot_date')->index();
            $table->decimal('total_cash_balance', 15, 2)->default(0);
            $table->decimal('total_outstanding_loans', 15, 2)->default(0);
            $table->decimal('total_pending_deposits', 15, 2)->default(0);
            $table->decimal('operational_reserve', 15, 2)->default(0);
            $table->decimal('idle_fund_amount', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['koperasi_id', 'snapshot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idle_fund_snapshots');
    }
};
