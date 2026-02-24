<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('alternate_saturday_weeks', 20)->nullable()->after('weekly_off_days');
        });
        DB::table('employees')->where('alternative_saturday_off', true)->update(['alternate_saturday_weeks' => '2,4']);
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('alternative_saturday_off');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('alternative_saturday_off')->default(false)->after('weekly_off_days');
        });
        DB::table('employees')->whereNotNull('alternate_saturday_weeks')->where('alternate_saturday_weeks', '!=', '')->update(['alternative_saturday_off' => true]);
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('alternate_saturday_weeks');
        });
    }
};
