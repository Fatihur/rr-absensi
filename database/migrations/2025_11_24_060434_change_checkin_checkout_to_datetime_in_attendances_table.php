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
        Schema::table('attendances', function (Blueprint $table) {
            // Change check_in and check_out from time to datetime
            $table->dateTime('check_in')->nullable()->change();
            $table->dateTime('check_out')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Revert back to time
            $table->time('check_in')->nullable()->change();
            $table->time('check_out')->nullable()->change();
        });
    }
};
