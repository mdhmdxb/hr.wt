<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 191);
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('open');
            $table->timestamp('closed_at')->nullable();
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->timestamps();
        });

        Schema::create('job_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->constrained()->cascadeOnDelete();
            $table->string('name', 191);
            $table->string('email', 191);
            $table->string('phone', 50)->nullable();
            $table->string('stage', 50)->default('applied');
            $table->text('notes')->nullable();
            $table->timestamp('interview_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_candidates');
        Schema::dropIfExists('job_openings');
    }
};
