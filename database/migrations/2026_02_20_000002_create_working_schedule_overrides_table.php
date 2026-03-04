<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_schedule_overrides', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "Ramadan"
            $table->date('start_date');
            $table->date('end_date');
            $table->string('work_start', 5)->nullable(); // e.g. "09:00"
            $table->string('work_end', 5)->nullable();   // e.g. "15:00" (2 hours less)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_schedule_overrides');
    }
};
