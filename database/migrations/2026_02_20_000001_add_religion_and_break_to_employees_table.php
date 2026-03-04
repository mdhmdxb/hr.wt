<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('religion', 50)->nullable()->after('gender');
            $table->unsignedSmallInteger('break_minutes')->nullable()->after('shift_end')->comment('Daily break in minutes, e.g. 0 for Muslim (Ramadan), 30 for others');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['religion', 'break_minutes']);
        });
    }
};
