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
        Schema::table('chats', function (Blueprint $table) {
            $table->string('chat_type')->default('public')->after('room');
            $table->unsignedBigInteger('parent_id')->nullable()->after('chat_type');
            $table->json('attachments')->nullable()->after('parent_id');
            $table->boolean('is_read')->default(false)->after('attachments');
            $table->unsignedBigInteger('solicitud_id')->nullable()->after('is_read');
            
            $table->foreign('parent_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('solicitud_id')->references('id')->on('solicitudes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['solicitud_id']);
            $table->dropColumn(['chat_type', 'parent_id', 'attachments', 'is_read', 'solicitud_id']);
        });
    }
};
