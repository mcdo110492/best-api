<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Inquiry;

class QuotationClients implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $inquiryId;

    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($userId,$inquiryId)
    {   
        $this->userId = $userId;
        $this->inquiryId = $inquiryId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('quotation-client.'.$this->userId);
    }

    public function broadcastWith(){

        $inquiry = Inquiry::with(['details','quotations'])->where(['inquiryId' => $this->inquiryId])->get();

        return ['inquiry' => $inquiry];

    }

}
