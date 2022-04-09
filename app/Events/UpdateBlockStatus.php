<?php

namespace App\Events;

use App\Models\Monitoring;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateBlockStatus implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Monitoring
     */
    public $monitoring;

    /**
     * @var string
     */
    private $event;

    /**
     * Create a new event instance.
     *
     * @param Monitoring $monitoring
     * @param string $event
     *
     * @return void
     */
    public function __construct(Monitoring $monitoring, string $event)
    {
        $this->monitoring = $monitoring;
        $this->event = $event;
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * @return array
     */
    public function tags(): array
    {
        return [
            'update-monitoring',
            'monitoring: ' . $this->monitoring->id,
            'event: ' . $this->event
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|PrivateChannel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("user.1");
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return "monitoring.{$this->event}";
    }
}
