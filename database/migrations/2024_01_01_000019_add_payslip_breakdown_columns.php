<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->decimal('accommodation', 12, 2)->default(0)->after('basic_salary');
            $table->decimal('transportation', 12, 2)->default(0)->after('accommodation');
            $table->decimal('food_allowance', 12, 2)->default(0)->after('transportation');
            $table->decimal('other_allowances', 12, 2)->default(0)->after('food_allowance');
            $table->decimal('bonus', 12, 2)->default(0)->after('other_allowances');
            $table->unsignedTinyInteger('days_worked')->nullable()->after('bonus');
            $table->unsignedTinyInteger('days_off')->nullable()->after('days_worked');
            $table->unsignedTinyInteger('holiday')->nullable()->after('days_off');
            $table->unsignedTinyInteger('annual_leave')->nullable()->after('holiday');
            $table->unsignedTinyInteger('unpaid_leave')->nullable()->after('annual_leave');
            $table->decimal('overtime_hours', 6, 2)->default(0)->after('unpaid_leave');
            $table->decimal('overtime_premium', 12, 2)->default(0)->after('overtime_hours')->comment('Premium: off days or night time per MOHRE UAE');
            $table->decimal('overtime_bonus_transport_food', 12, 2)->default(0)->after('overtime_premium');
            $table->decimal('salary_adjustment', 12, 2)->default(0)->after('overtime_bonus_transport_food')->comment('Deduction / salary adjustment');
            $table->decimal('total_wps_salary', 12, 2)->nullable()->after('net_pay')->comment('Total WPS Salary');
            $table->text('remarks')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropColumn([
                'accommodation', 'transportation', 'food_allowance', 'other_allowances', 'bonus',
                'days_worked', 'days_off', 'holiday', 'annual_leave', 'unpaid_leave',
                'overtime_hours', 'overtime_premium', 'overtime_bonus_transport_food',
                'salary_adjustment', 'total_wps_salary', 'remarks',
            ]);
        });
    }
};
