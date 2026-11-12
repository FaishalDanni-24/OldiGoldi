<?php

namespace Database\Factories\Chats;

use App\Models\Chats\ChatMessage;
use App\Models\Chats\Conversation;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    protected $model = ChatMessage::class;

    public function definition(): array
    {
        return [
            'sender_id' => User::factory(),
            'conversation_id' => Conversation::factory(),
            'message_type' => fake()->randomElement(['text', 'image', 'file', 'system']),
            'message_text' => fake()->optional()->sentence(),
            'attachment_url' => fake()->optional()->url(),
            'is_read' => fake()->boolean(40),
            'sent_at' => fake()->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
