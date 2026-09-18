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
        Schema::create('cash_periods', function (Blueprint $table) {
            $table->id();
            $table->string('academic_year', 9);
            $table->string('semester', 10);
            $table->unsignedSmallInteger('week_number');
            $table->string('name', 100);
            $table->decimal('amount', 15, 2);
            $table->date('start_date');
            $table->date('due_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->unique(['academic_year', 'semester', 'week_number'], 'uq_cash_periods_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_periods');
    }
};
