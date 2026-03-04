<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('public_holidays', function (Blueprint $table) {
            $table->date('end_date')->nullable()->after('date');
        });
        // Rename date to start_date for clarity (optional: keep 'date' as alias for start)
        // We keep 'date' as start; end_date is optional. If null, single day.
    }

    public function down(): void
    {
        Schema::table('public_holidays', function (Blueprint $table) {
            $table->dropColumn('end_date');
        });
    }
};
