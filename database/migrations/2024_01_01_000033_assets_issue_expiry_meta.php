<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->date('issue_date')->nullable()->after('notes');
            $table->date('expiry_date')->nullable()->after('issue_date');
            $table->json('meta')->nullable()->after('expiry_date')->comment('Type-specific: e.g. plate_number, registration_number for vehicles');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['issue_date', 'expiry_date', 'meta']);
        });
    }
};
