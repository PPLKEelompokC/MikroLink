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
        Schema::create('fund_allocations', function (Blueprint $table) {
            $table->id();
            $table->string('koperasi_id');
            $table->foreign('koperasi_id')->references('id_koperasi')->on('koperasi')->onDelete('cascade');
            $table->foreignId('snapshot_id')->constrained('idle_fund_snapshots')->cascadeOnDelete();
            $table->decimal('recommended_amount', 15, 2)->default(0);
            $table->string('allocation_category');
            $table->decimal('confidence_score', 5, 2)->default(0);
            $table->text('reasoning');
            $table->string('ai_model_used');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fund_allocations');
    }
};
