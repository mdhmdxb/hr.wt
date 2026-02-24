<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->decimal('accommodation', 12, 2)->default(0)->after('basic_salary');
            $table->decimal('transportation', 12, 2)->default(0)->after('accommodation');
            $table->decimal('food_allowance', 12, 2)->default(0)->after('transportation');
            $table->decimal('other_allowances', 12, 2)->default(0)->after('food_allowance');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['accommodation', 'transportation', 'food_allowance', 'other_allowances']);
        });
    }
};
