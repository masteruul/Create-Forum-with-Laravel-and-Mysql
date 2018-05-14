<?php

namespace App\Listeners;

use App\Events\Event\ThreadReceviedNewReply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifySubscribers
{

    /**
     * Handle the event.
     *
     * @param  ThreadReceviedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceviedNewReply $event)
    {
       $event->reply->thread->subscriptions
        ->where('user_id','!=',$event->reply->user_id)
        ->each
        ->notify($event->reply);
    }
}
