<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    function non_administreators_may_not_lock_threads()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $thread = create('App\Thread',['user_id'=>auth()->id()]);

        //hit endpoint
        $this->post(route('locked-threads.store',$thread))->assertStatus(403);

        $this->assertFalse(!! $thread->fresh()->locked);

    }

    /** @test */
    function administrators_can_lock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread',['user_id'=>auth()->id()]);

        //hit endpoint
        $this->post(route('locked-threads.store',$thread));
        
        $this->assertTrue(!! $thread->fresh()->locked);
    
    }

    /** @test */
    function administrators_can_unlock_threads()
    {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread',['user_id'=>auth()->id(),'locked'=>true]);

        //hit endpoint
        $this->delete(route('locked-threads.destroy',$thread));
    
        $this->assertFalse(!! $thread->fresh()->locked,'fail cuk');
    
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies()
    {
        $this->signIn();

        $thread = create('App\Thread',['locked'=>true]);

        $this->post($thread->path().'/replies',[
            'body' => 'Foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
