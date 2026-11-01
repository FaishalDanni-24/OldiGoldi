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
            $table->id('message_id');
            $table->unsignedBigInteger('sender_id');
            $table->foreignId('conversation_id');
            $table->foreign('sender_id')->references('user_id')->on('users');
            $table->enum('message_type', array('text', 'image', 'file', 'system'))->default('text');
            $table->text('message_text')->nullable();
            $table->string('attachment_url', 1000)->nullable();
            $table->boolean('is_read')->default(0);
            $table->timestamp('sent_at')->useCurrent();
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
