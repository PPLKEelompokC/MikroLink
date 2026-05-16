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
        Schema::table('capital_logs', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
            $table->enum('transaction_type', ['deposit', 'withdrawal'])->default('deposit')->after('type');
            $table->string('new_type')->nullable()->after('type');
        });

        \Illuminate\Support\Facades\DB::table('capital_logs')->update([
            'new_type' => \Illuminate\Support\Facades\DB::raw("
                CASE 
                    WHEN type = 'Simpanan' THEN 'simpanan_wajib'
                    WHEN type = 'Penyesuaian Modal' THEN 'hibah'
                    WHEN type = 'Dana Darurat' THEN 'dana_cadangan'
                    WHEN type = 'Pinjaman Usaha' THEN 'pinjaman_usaha'
                    ELSE 'simpanan_wajib' 
                END
            ")
        ]);

        Schema::table('capital_logs', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('capital_logs', function (Blueprint $table) {
            $table->renameColumn('new_type', 'type');
        });
        
        Schema::table('capital_logs', function (Blueprint $table) {
            $table->string('type')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capital_logs', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'transaction_type']);
            $table->string('old_type')->nullable();
        });
        
        \Illuminate\Support\Facades\DB::table('capital_logs')->update([
            'old_type' => \Illuminate\Support\Facades\DB::raw("
                CASE 
                    WHEN type = 'simpanan_wajib' THEN 'Simpanan'
                    WHEN type = 'hibah' THEN 'Penyesuaian Modal'
                    WHEN type = 'dana_cadangan' THEN 'Dana Darurat'
                    WHEN type = 'pinjaman_usaha' THEN 'Pinjaman Usaha'
                    ELSE 'Simpanan' 
                END
            ")
        ]);
        
        Schema::table('capital_logs', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        
        Schema::table('capital_logs', function (Blueprint $table) {
            $table->renameColumn('old_type', 'type');
        });
    }
};
