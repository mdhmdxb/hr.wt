<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('nationality')->nullable()->after('phone');
            $table->string('gender', 20)->nullable()->after('nationality');
            $table->text('permanent_address')->nullable()->after('gender');
            $table->string('emergency_contact_name')->nullable()->after('permanent_address');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['nationality', 'gender', 'permanent_address', 'emergency_contact_name', 'emergency_contact_phone']);
        });
    }
};

