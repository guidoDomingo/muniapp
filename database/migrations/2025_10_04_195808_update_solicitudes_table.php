<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('estado');
            $table->json('attachments')->nullable()->after('formulario');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->after('attachments');
            $table->date('estimated_completion_date')->nullable()->after('priority');
            $table->date('actual_completion_date')->nullable()->after('estimated_completion_date');
            $table->unsignedBigInteger('assigned_to')->nullable()->after('actual_completion_date');
            $table->string('tracking_code')->unique()->nullable()->after('assigned_to');
            
            $table->foreign('status_id')->references('id')->on('tramite_statuses')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['assigned_to']);
            $table->dropColumn(['status_id', 'attachments', 'priority', 'estimated_completion_date', 'actual_completion_date', 'assigned_to', 'tracking_code']);
        });
    }
};
