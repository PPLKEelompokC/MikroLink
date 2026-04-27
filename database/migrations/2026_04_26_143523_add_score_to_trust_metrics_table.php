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
        if (Schema::hasTable('trust_metrics')) {
            Schema::table('trust_metrics', function (Blueprint $table) {
                if (!Schema::hasColumn('trust_metrics', 'score')) {
                    $table->integer('score')->nullable()->after('user_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trust_metrics')) {
            Schema::table('trust_metrics', function (Blueprint $table) {
                if (Schema::hasColumn('trust_metrics', 'score')) {
                    $table->dropColumn('score');
                }
            });
        }
    }
};