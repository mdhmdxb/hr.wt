<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->date('issue_date')->nullable()->after('file_path');
            $table->foreignId('renewal_of_id')->nullable()->after('employee_id')->constrained('employee_documents')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employee_documents', function (Blueprint $table) {
            $table->dropForeign(['renewal_of_id']);
            $table->dropColumn(['issue_date', 'renewal_of_id']);
        });
    }
};
