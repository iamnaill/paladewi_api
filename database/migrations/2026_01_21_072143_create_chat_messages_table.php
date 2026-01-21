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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('session_id');
            $table->enum('role', ['user', 'assistant', 'system'])->index();
            $table->longText('content');
            $table->json('meta')->nullable(); // citations/token/latency opsional
            $table->timestamps();

            $table->foreign('session_id')
                ->references('id')->on('chat_sessions')
                ->onDelete('cascade');

            $table->index(['session_id', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
