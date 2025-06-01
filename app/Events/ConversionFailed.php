<?php

namespace App\Events;

use App\Models\Conversion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversionFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Conversion $conversion)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->conversion->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'conversion_id' => $this->conversion->id,
            'status' => 'failed',
            'message' => 'Conversion failed: ' . $this->conversion->error_message,
        ];
    }
}
