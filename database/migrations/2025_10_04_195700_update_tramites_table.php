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
        Schema::table('tramites', function (Blueprint $table) {
            $table->unsignedBigInteger('department_id')->nullable()->after('descripcion');
            $table->json('form_fields')->nullable()->after('department_id');
            $table->json('required_documents')->nullable()->after('form_fields');
            $table->integer('estimated_days')->nullable()->after('required_documents');
            $table->decimal('cost', 10, 2)->default(0)->after('estimated_days');
            $table->boolean('is_active')->default(true)->after('cost');
            $table->json('workflow_steps')->nullable()->after('is_active');
            
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tramites', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn(['department_id', 'form_fields', 'required_documents', 'estimated_days', 'cost', 'is_active', 'workflow_steps']);
        });
    }
};
