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
        if (! Schema::hasTable('trust_metrics')) {
            Schema::create('trust_metrics', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->integer('score')->nullable();
                $table->integer('participation_score')->default(0);
                $table->integer('integrity_score')->default(0);
                $table->integer('reliability_score')->default(0);
                $table->decimal('final_index', 5, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('trust_metrics', function (Blueprint $table) {
                if (! Schema::hasColumn('trust_metrics', 'score')) {
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