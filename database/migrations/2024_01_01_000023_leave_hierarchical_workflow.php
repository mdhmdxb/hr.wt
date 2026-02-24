<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->json('workflow_steps')->nullable()->after('is_paid');
        });

        Schema::create('leave_approval_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_request_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('step_order')->default(1);
            $table->string('approver_type', 50); // reporting_manager, hr, accounts, admin
            $table->string('status', 20)->default('pending'); // pending, approved, rejected
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_approval_steps');
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn('workflow_steps');
        });
    }
};
