<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatMessageSeeder extends Seeder
{
    public function run(): void
    {
        // Minimal: only required attributes (sender_id, conversation_id)
        DB::table('chat_messages')->insert([
            'sender_id' => 2,
            'conversation_id' => 1,
        ]);

        // Full: all attributes filled
        DB::table('chat_messages')->insert([
            'sender_id' => 1,
            'conversation_id' => 2,
            'message_type' => 'text',
            'message_text' => 'Halo, ada yang bisa dibantu?',
            'attachment_url' => null,
            'is_read' => 0,
            'sent_at' => now(),
        ]);
    }
}
