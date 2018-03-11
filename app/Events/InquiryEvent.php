<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class InquiryEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $id;

    public $name;

    public $email;

    public $date;

    public $clientNumber;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($id,$name,$email,$date,$clientNumber)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->date = $date;
        $this->clientNumber = $clientNumber;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('inquiry');
    }
}
