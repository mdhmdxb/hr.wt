<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->string('code', 50)->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('budget', 14, 2)->nullable();
            $table->string('status', 30)->default('active');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('role', 50)->nullable();
            $table->timestamps();
            $table->unique(['employee_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_project');
        Schema::dropIfExists('projects');
    }
};
