<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('weekly_off_days', 100)->nullable()->after('status'); // e.g. "saturday,sunday" or "fri,sat"
            $table->boolean('alternative_saturday_off')->default(false)->after('weekly_off_days');
            $table->string('shift_start', 5)->nullable()->after('alternative_saturday_off'); // e.g. "09:00"
            $table->string('shift_end', 5)->nullable()->after('shift_start'); // e.g. "17:00"
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['weekly_off_days', 'alternative_saturday_off', 'shift_start', 'shift_end']);
        });
    }
};
