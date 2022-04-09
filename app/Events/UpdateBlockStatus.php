<?php

namespace App\Events;

use App\Models\Monitoring;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateBlockStatus implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Monitoring
     */
    public $monitoring;

    /**
     * Create a new event instance.
     *
     * @param Monitoring $monitoring
     *
     * @return void
     */
    public function __construct(Monitoring $monitoring)
    {
        $this->monitoring = $monitoring;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|PrivateChannel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("monitoring.{$this->monitoring->id}");
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'monitoring.updated';
    }
}
