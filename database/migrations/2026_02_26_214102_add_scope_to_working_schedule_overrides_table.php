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
        Schema::table('working_schedule_overrides', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('end_date')->constrained()->nullOnDelete();
            $table->foreignId('site_id')->nullable()->after('branch_id')->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->after('site_id')->constrained()->nullOnDelete();
            $table->foreignId('employee_id')->nullable()->after('project_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('working_schedule_overrides', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropForeign(['site_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['employee_id']);
        });
    }
};
