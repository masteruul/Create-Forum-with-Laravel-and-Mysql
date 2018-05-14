<?php

namespace App\Listeners;

use App\User;
use App\Events\Event\ThreadReceviedNewReply;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\YouWereMentioned;

class NotifyMentionedUsers
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ThreadReceviedNewReply  $event
     * @return void
     */
    public function handle(ThreadReceviedNewReply $event)
    {
        
        User::whereIn('name',$event->reply->mentionedUsers())
            ->get()
            ->each(function($user)use($event){
                $user->notify(new YouWereMentioned($event->reply));
            });

    }
}
