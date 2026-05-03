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
        Schema::table('koperasi', function (Blueprint $table) {
            $table->decimal('total_outstanding_loans', 15, 2)->default(0)->after('saldo_kas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('koperasi', function (Blueprint $table) {
            $table->dropColumn('total_outstanding_loans');
        });
    }
};
