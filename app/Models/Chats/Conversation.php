<?php

namespace App\Models\Chats;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'customer_id',
    'admin_id',
    'order_id',
    'product_id',
    'conversation_status'
])]
#[Table(key: 'conversation_id')]
class Conversation extends Model
{
    /** @use HasFactory<\Database\Factories\ConversationFactory> */
    use HasFactory;
}
