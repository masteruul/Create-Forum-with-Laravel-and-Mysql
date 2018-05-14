<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        
        $response = $this->get('/threads')
            ->assertSee($this->thread->title);

    }

    /** @test*/
    public function a_user_can_read_single_thread()
    {
        $response = $this->get($this->thread->path())
            ->assertSee($this->thread->title);
    }


    /** @test */
    public function a_user_can_filter_threads_according_to_a_channel(){
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread',['channel_id'=>$channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/'.$channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_any_username()
    {   
        $this->signIn(create('App\User',['name'=>'masteruul']));

        $threadByUul = create('App\Thread',['user_id'=>auth()->id()]);
        $threadNotByUul = create('App\Thread');

        $this->get('threads?by=masteruul')
            ->AssertSee($threadByUul->title)
            ->AssertDontSee($threadNotByUul->title);
    }


    /** @test */
    function a_user_can_filter_threads_by_popularity()
    {
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply',['thread_id'=>$threadWithTwoReplies->id],2);

        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply',['thread_id'=>$threadWithThreeReplies->id],3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        $this->assertEquals([3,2,0],array_column($response['data'],'replies_count'));
        
    }

    /** @test */
    function a_user_can_filter_threads_by_those_that_are_unanswered()
    {
        $thread = create('App\Thread');
        create('App\Reply',['thread_id'=>$thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1,$response['data']);
    }

    /** @test */
    function a_user_can_request_all_replies_for_given_thread()
    {
        $thread = create('App\Thread');
        create('App\Reply',['thread_id'=>$thread->id],2);

        $response = $this->getJson($thread->path().'/replies')->json();
        $this->assertCount(2,$response['data']);
    }

    /** @test */
    function a_thread_can_be_subscribe_to()
    {
        $thread = create('App\Thread');

        //$this->signIn();

        $thread->subscribe($userId = 1);

        $this->assertEquals(
            1,
            $thread->subscriptions()->where('user_id',$userId)->count()
        );
    }

    /** @test */
    function a_thread_can_be_unsubscribe_to()
    {
        $thread = create('App\Thread');

        $thread->subscribe($userId=1);

        $thread->unsubscribe($userId);

        $this->assertCount(0,$thread->subscriptions);
    }

}
