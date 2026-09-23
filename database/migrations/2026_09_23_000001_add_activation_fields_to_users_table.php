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
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->string('activation_token', 64)->nullable()->index()->after('remember_token');
            $table->timestamp('activation_expires_at')->nullable()->after('activation_token');
            $table->timestamp('activated_at')->nullable()->after('activation_expires_at');
        });

        // Ensure all existing accounts that already have a password are automatically marked as activated
        DB::table('users')
            ->whereNotNull('password')
            ->whereNull('activated_at')
            ->update([
                'activated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable(false)->change();
            $table->dropIndex(['activation_token']);
            $table->dropColumn([
                'activation_token',
                'activation_expires_at',
                'activated_at',
            ]);
        });
    }
};
