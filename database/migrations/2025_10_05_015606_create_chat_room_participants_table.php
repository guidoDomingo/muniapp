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
        Schema::create('chat_room_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained('chat_rooms')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('role', ['member', 'admin', 'moderator'])->default('member');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('permissions')->nullable(); // Permisos específicos del participante
            $table->timestamps();
            
            // Evitar duplicados
            $table->unique(['chat_room_id', 'user_id']);
            $table->index(['user_id', 'is_active']);
            $table->index('chat_room_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_room_participants');
    }
};
