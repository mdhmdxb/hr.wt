<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->date('actual_return_date')->nullable()->after('end_date')->comment('Date employee actually returned; used to compute early return (add days back) or overstay (unpaid days).');
            $table->unsignedSmallInteger('overstay_days')->nullable()->after('actual_return_date')->comment('Days returned after approved end date; treated as unpaid leave.');
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['actual_return_date', 'overstay_days']);
        });
    }
};
