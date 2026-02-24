<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('default_shift_start', 5)->nullable()->after('address');
            $table->string('default_shift_end', 5)->nullable()->after('default_shift_start');
            $table->decimal('default_accommodation', 12, 2)->nullable()->after('default_shift_end');
            $table->decimal('default_transportation', 12, 2)->nullable()->after('default_accommodation');
            $table->decimal('default_food_allowance', 12, 2)->nullable()->after('default_transportation');
            $table->decimal('default_other_allowances', 12, 2)->nullable()->after('default_food_allowance');
        });
        Schema::table('sites', function (Blueprint $table) {
            $table->string('default_shift_start', 5)->nullable()->after('address');
            $table->string('default_shift_end', 5)->nullable()->after('default_shift_start');
            $table->decimal('default_accommodation', 12, 2)->nullable()->after('default_shift_end');
            $table->decimal('default_transportation', 12, 2)->nullable()->after('default_accommodation');
            $table->decimal('default_food_allowance', 12, 2)->nullable()->after('default_transportation');
            $table->decimal('default_other_allowances', 12, 2)->nullable()->after('default_food_allowance');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'default_shift_start', 'default_shift_end',
                'default_accommodation', 'default_transportation', 'default_food_allowance', 'default_other_allowances',
            ]);
        });
        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn([
                'default_shift_start', 'default_shift_end',
                'default_accommodation', 'default_transportation', 'default_food_allowance', 'default_other_allowances',
            ]);
        });
    }
};
