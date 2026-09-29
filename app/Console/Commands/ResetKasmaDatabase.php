<?php

namespace App\Console\Commands;

use App\Models\CashPeriod;
use App\Models\FinancialTransaction;
use App\Models\Payment;
use App\Models\StudentDue;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetKasmaDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kasma:reset-database {--force : Abaikan konfirmasi interaktif}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan seluruh data dummy/transaksi kas dan mahasiswa untuk memulai testing data asli';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('PERINGATAN: Tindakan ini akan menghapus SEMUA transaksi kas, pembayaran, tagihan, dan data mahasiswa. Lanjutkan?', false)) {
                $this->info('Operasi dibatalkan.');
                return Command::SUCCESS;
            }
        }

        $this->info('Memulai pembersihan data KASMA...');

        DB::transaction(function () {
            // 1. Delete all transactions
            $txCount = DB::table('financial_transactions')->count();
            DB::table('financial_transactions')->delete();
            $this->line(" - Menghapus {$txCount} mutasi buku kas.");

            // 2. Delete all payments
            $payCount = DB::table('payments')->count();
            DB::table('payments')->delete();
            $this->line(" - Menghapus {$payCount} berkas pembayaran & verifikasi.");

            // 3. Delete all student dues
            $dueCount = DB::table('student_dues')->count();
            DB::table('student_dues')->delete();
            $this->line(" - Menghapus {$dueCount} kewajiban iuran pekanan.");

            // 4. Delete all students
            $studentCount = DB::table('users')->where('role', 'mahasiswa')->count();
            DB::table('users')->where('role', 'mahasiswa')->delete();
            $this->line(" - Menghapus {$studentCount} akun mahasiswa.");

            // 5. Ensure at least one active Bendahara account exists
            if (! DB::table('users')->where('role', 'bendahara')->exists()) {
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
                $this->line(' - Membuat akun default Bendahara (bendahara@kasma.edu / password).');
            } else {
                $this->line(' - Akun Bendahara yang ada tetap dipertahankan.');
            }

            // 6. Reset active period
            DB::table('cash_periods')->update(['is_active' => false]);
            $firstPeriod = DB::table('cash_periods')->orderBy('week_number', 'asc')->first();
            if ($firstPeriod) {
                DB::table('cash_periods')->where('id', $firstPeriod->id)->update(['is_active' => true]);
                $this->line(" - Mengatur {$firstPeriod->name} sebagai periode aktif.");
            }
        });

        $this->info('Sukses! Data KASMA berhasil dibersihkan.');
        $this->info('Status saat ini: Saldo Kas = Rp 0 | Mahasiswa = 0 | Akun Bendahara = Siap Digunakan.');

        return Command::SUCCESS;
    }
}
