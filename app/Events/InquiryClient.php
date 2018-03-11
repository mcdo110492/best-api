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

class InquiryClient implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $clientId;

    public $inquiryId;

    public $adminId;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($clientId,$inquiryId,$adminId)
    {
        $this->clientId = $clientId;
        $this->inquiryId = $inquiryId;
        $this->adminId = $adminId;
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

        $getAdmin = DB::table('users')->where(['userId' => $this->adminId])->get()->first();

        $getInquiry = DB::table('inquiry')->where(['inquiryId' => $this->inquiryId])->get();

        return ['adminName' => $getAdmin->fullName, 'inquiry' => $getInquiry];
        
    }
}
