<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTokenReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $chunk;
    public int $messageId;

    public function __construct(int $messageId, string $chunk)
    {
        $this->messageId = $messageId;
        $this->chunk = $chunk;
    }

    public function broadcastOn(): array
    {
        // Broadcast on a private channel specific to the message being generated
        return [
            new PrivateChannel('chat.'.$this->messageId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-token';
    }
}