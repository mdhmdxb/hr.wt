<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add religion if missing. Place after gender when available; otherwise append.
            if (! Schema::hasColumn('employees', 'religion')) {
                $religion = $table->string('religion', 50)->nullable();
                if (Schema::hasColumn('employees', 'gender')) {
                    $religion->after('gender');
                }
            }

            // Add break_minutes if missing. Place after shift_end when available; otherwise append.
            if (! Schema::hasColumn('employees', 'break_minutes')) {
                $break = $table->unsignedSmallInteger('break_minutes')->nullable()
                    ->comment('Daily break in minutes, e.g. 0 for Muslim (Ramadan), 30 for others');
                if (Schema::hasColumn('employees', 'shift_end')) {
                    $break->after('shift_end');
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['religion', 'break_minutes']);
        });
    }
};
