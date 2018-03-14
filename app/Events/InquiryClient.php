<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Facades\DB;

use App\Inquiry;

class InquiryClient implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $clientId;

    public $inquiryId;

    public $status;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($clientId,$inquiryId,$status)
    {
        $this->clientId = $clientId;
        $this->inquiryId = $inquiryId;
        $this->status = $status;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('inquiry-client.'.$this->clientId);
    }


    /**
     * Get The Admin and Date Confirmed by Admin
     * @return array
     */
    public function broadcastWith() {
        $where = ['inquiryId' => $this->inquiryId];
        $q = Inquiry::with(['details','admin'])->where($where);
        $get = $q->get()->first();

        return ['inquiry' => $get, 'status' => $this->status];
        
    }
}
