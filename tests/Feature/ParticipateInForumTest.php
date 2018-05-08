<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ParticipateInForumTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    function unauthenticated_users_may_not_add_replies()
    {
       
        $this->withExceptionHandling()
            ->post('/threads/some-channel/1/replies',[])
            ->assertRedirect('/login');

    }
    
    /** @test */
    function a_aunticated_user_may_participate_in_forum_threads()
    {
        //given we have auntitaced user
        $this->signIn();

        //and exitsting thread
        $thread = create('App\Thread');
        
        //when the user add reply to thread
        $reply=make('App\Reply');
        $this->post($thread->path().'/replies', $reply->toArray());
        
        //then reply should be visible on the page
        $this->get($thread->path())
            ->assertSee($reply->body);
         
    }

    /** @test */
    function a_reply_requires_a_body()
    {
        $this->withExceptionHandling()->signIn();
        
        $thread = create('App\Thread');
        
        //when the user add reply to thread
        $reply=make('App\Reply',['body'=>null]);
        
        $this->post($thread->path().'/replies', $reply->toArray())
            ->assertSessionHasErrors('body');
        
    }
}
