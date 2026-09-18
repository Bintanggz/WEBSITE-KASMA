<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_due_id')->constrained('student_dues')->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('payment_method', 30);
            $table->string('proof_file_path', 255)->nullable();
            $table->timestampTz('payment_date')->useCurrent();
            $table->string('status', 20)->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('verified_at')->nullable();
            $table->timestamps();

            $table->index('status', 'idx_payments_status');
            $table->index('proof_file_path', 'idx_payments_proof_file_path');
        });

        // Enforce at most one approved payment per student_due
        DB::statement('CREATE UNIQUE INDEX uq_payments_one_approved_per_due ON payments (student_due_id) WHERE status = \'approved\';');

        // Prevent multiple simultaneous pending payments for the same student_due
        DB::statement('CREATE UNIQUE INDEX uq_payments_one_pending_per_due ON payments (student_due_id) WHERE status = \'pending\';');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
