<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_salary_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->date('effective_from');
            $table->decimal('basic_salary', 14, 2)->nullable();
            $table->decimal('accommodation', 14, 2)->nullable();
            $table->decimal('transportation', 14, 2)->nullable();
            $table->decimal('food_allowance', 14, 2)->nullable();
            $table->decimal('other_allowances', 14, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_salary_revisions');
    }
};
