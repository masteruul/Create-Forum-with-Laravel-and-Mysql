<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;
    public function setUp()
    {
        parent::setUp();

        $this->signIn();
    }
    
    /** @test */
    function a_notification_is_preapred_when_a_subscribed_thread_receives_a_new_reply()
    {
        $this->signIn();

        $thread = create('App\Thread')->subscribe();

        $this->assertCount(0,auth()->user()->notifications);
        $thread->addReply([
            'user_id'=> auth()->id(),

            'body'=>'Some Reply here'
        ]);
        $this->assertCount(0,auth()->user()->notifications);
    }

    /** @test */
    function a_notification_is_preapred_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        $this->signIn();

        $thread = create('App\Thread')->subscribe();

        $thread->addReply([
            'user_id'=> auth()->id(),

            'body'=>'Some Reply here'
        ]);
        $this->assertCount(0,auth()->user()->fresh()->notifications);

        $thread->addReply([
            'user_id'=> create('App\User')->id,

            'body'=>'Some Reply here'
        ]);

        $this->assertCount(1,auth()->user()->fresh()->notifications);
        
    }

    /** @test */
    function a_user_can_fect_their_unread_notifications()
    {
        create(DatabaseNotification::class);

        $this->assertCount(
            1,
            $this->getJson("/profiles/".auth()->user()->name."/notifications")->json());
    }

    /** @test */
    function a_user_can_mark_a_notification_as_read()
    {
        //$this->signIn();
        create(DatabaseNotification::class);        

       tap(auth()->user(), function ($user){
            $this->assertCount(1,$user->unreadNotifications);

            $this->delete("/profiles/{$user->name}/notifications/".$user->unreadNotifications->first()->id);

            $this->assertCount(0,$user->fresh()->unreadNotifications);
       });

            }
}
