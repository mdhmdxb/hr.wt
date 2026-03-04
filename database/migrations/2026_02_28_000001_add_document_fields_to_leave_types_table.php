<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->boolean('allow_document')->default(false)->after('is_paid');
            $table->boolean('require_document')->default(false)->after('allow_document');
            $table->string('document_label')->nullable()->after('require_document');
        });
    }

    public function down(): void
    {
        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn(['allow_document', 'require_document', 'document_label']);
        });
    }
};

