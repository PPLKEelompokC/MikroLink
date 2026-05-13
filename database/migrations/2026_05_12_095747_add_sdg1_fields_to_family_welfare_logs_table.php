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
        Schema::table('family_welfare_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->cascadeOnDelete();
            $table->date('period_date')->after('user_id');
            $table->decimal('income_before', 15, 2)->default(0)->after('period_date');
            $table->decimal('income_after', 15, 2)->default(0)->after('income_before');
            $table->unsignedSmallInteger('dependents_count')->default(0)->after('income_after');
            $table->string('food_security_status')->after('dependents_count');
            $table->string('education_access_status')->after('food_security_status');
            $table->string('health_access_status')->after('education_access_status');
            $table->unsignedTinyInteger('welfare_score')->default(0)->after('health_access_status');
            $table->text('notes')->nullable()->after('welfare_score');

            $table->index(['user_id', 'period_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('family_welfare_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'period_date']);
            $table->dropConstrainedForeignId('user_id');
            $table->dropColumn([
                'period_date',
                'income_before',
                'income_after',
                'dependents_count',
                'food_security_status',
                'education_access_status',
                'health_access_status',
                'welfare_score',
                'notes',
            ]);
        });
    }
};
