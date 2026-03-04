<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Allow HR/Admin to control when employees can upload a replacement document
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->boolean('employee_can_upload_again')->default(false)->after('approved_at');
        });

        // Add date of birth to employees for birthday features
        Schema::table('employees', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('hire_date');
        });
    }

    public function down(): void
    {
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->dropColumn('employee_can_upload_again');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('date_of_birth');
        });
    }
};

