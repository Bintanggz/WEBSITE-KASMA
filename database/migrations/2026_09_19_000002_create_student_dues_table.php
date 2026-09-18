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
        Schema::create('student_dues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_period_id')->constrained('cash_periods')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('status', 20)->default('unpaid');
            $table->timestamps();

            $table->unique(['cash_period_id', 'user_id'], 'uq_student_dues_period_user');
            $table->index(['user_id', 'status'], 'idx_student_dues_user_status');
            $table->index(['cash_period_id', 'status'], 'idx_student_dues_period_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_dues');
    }
};
