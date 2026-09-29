<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Cleans all dummy student, payment, due, and transaction records so that KASMA
     * can start fresh for real students of class TI26A3.
     */
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        // 1. Delete all financial transactions (pembukuan kas)
        DB::table('financial_transactions')->delete();

        // 2. Delete all payment records (bukti transfer & verifikasi)
        DB::table('payments')->delete();

        // 3. Delete all student dues (kewajiban kas pekanan)
        DB::table('student_dues')->delete();

        // 4. Delete all students (hapus seluruh mahasiswa dummy / uji coba)
        DB::table('users')->where('role', 'mahasiswa')->delete();

        // 5. Ensure at least one active Bendahara account exists
        $bendahara = DB::table('users')->where('role', 'bendahara')->first();
        if (! $bendahara) {
            DB::table('users')->insert([
                'name' => 'Bendahara TI26A3',
                'nim' => '220400',
                'email' => 'bendahara@kasma.edu',
                'password' => Hash::make('password'),
                'role' => 'bendahara',
                'phone_number' => '082198765432',
                'is_active' => true,
                'activated_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($bendahara->name === 'Nadya Putri') {
            DB::table('users')->where('id', $bendahara->id)->update([
                'name' => 'Bendahara TI26A3',
            ]);
        }

        // 6. Reset active cash period to Week 1
        DB::table('cash_periods')->update(['is_active' => false]);
        $firstPeriod = DB::table('cash_periods')->orderBy('week_number', 'asc')->first();
        if ($firstPeriod) {
            DB::table('cash_periods')->where('id', $firstPeriod->id)->update(['is_active' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-time data cleanup is irreversible
    }
};
