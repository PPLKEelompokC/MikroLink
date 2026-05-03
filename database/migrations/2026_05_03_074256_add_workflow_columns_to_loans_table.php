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
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'reviewed_by_admin')) {
                $table->unsignedBigInteger('reviewed_by_admin')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('loans', 'admin_note')) {
                $table->text('admin_note')->nullable()->after('reviewed_by_admin');
            }
            if (!Schema::hasColumn('loans', 'admin_reviewed_at')) {
                $table->timestamp('admin_reviewed_at')->nullable()->after('admin_note');
            }
            if (!Schema::hasColumn('loans', 'reviewed_by_manajer')) {
                $table->unsignedBigInteger('reviewed_by_manajer')->nullable()->after('admin_reviewed_at');
            }
            if (!Schema::hasColumn('loans', 'manajer_note')) {
                $table->text('manajer_note')->nullable()->after('reviewed_by_manajer');
            }
            if (!Schema::hasColumn('loans', 'manajer_reviewed_at')) {
                $table->timestamp('manajer_reviewed_at')->nullable()->after('manajer_note');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $columns = [
                'reviewed_by_admin',
                'admin_note',
                'admin_reviewed_at',
                'reviewed_by_manajer',
                'manajer_note',
                'manajer_reviewed_at',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('loans', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};