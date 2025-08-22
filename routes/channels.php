<?php

use App\Models\Message;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{messageId}', function ($user, $messageId) {
    // Verify that the current user is the owner of the message's conversation
    $message = Message::findOrFail($messageId);
    return (int) $user->id === (int) $message->conversation->user_id;
});
