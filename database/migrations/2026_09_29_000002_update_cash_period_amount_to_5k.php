<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Updates cash period dues and student dues for class TI26A3 to Rp 5.000 weekly.
     */
    public function up(): void
    {
        // 1. Update all existing cash periods to Rp 5.000
        DB::table('cash_periods')->update([
            'amount' => '5000.00',
        ]);

        // 2. Update existing student dues to Rp 5.000
        DB::table('student_dues')->update([
            'amount' => '5000.00',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('cash_periods')->update([
            'amount' => '10000.00',
        ]);

        DB::table('student_dues')->update([
            'amount' => '10000.00',
        ]);
    }
};
