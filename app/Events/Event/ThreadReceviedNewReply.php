<?php

namespace App\Events\Event;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class ThreadReceviedNewReply
{
    use Dispatchable, SerializesModels;

    public $reply;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($reply)
    {
        $this->reply = $reply;
    }

}
