<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Redis; 
use App\Notifications\ThreadWasUpdated;
use App\Thread;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;
    public function setUp(){
        parent::setUp();
        $this->thread = factory('App\Thread')->create();

    }

    /** @test */
    public function a_thread_has_a_path()
    {
        $thread = create('App\Thread');

        $this->assertEquals(
            "/threads/{$thread->channel->slug}/{$thread->slug}",$thread->path()
        );
    }

    /** @test */
    public function a_thread_has_a_creator()
    {
    $thread = factory('App\Thread')->create();

    $this->assertInstanceOf('App\User',$this->thread->creator);
    }

    /** @test */
    public function a_thread_has_replies()
    {
        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->thread->replies);
    }

  
    /** @test */
    public function a_thread_can_add_a_reply()
    {
        $this->thread->addReply([
            'body' => 'Foobar',
            'user_id' => 1
        ]);

        $this->assertCount(1,$this->thread->replies);
    }

    /** @test */
    public function a_thread_notify_all_registered_subscribers_when_a_reply_is_added()
    {
        Notification::fake();
        $this->signIn()
            ->thread
            ->subscribe()
            ->addReply([
                'body' => 'foobar',
                'user_id' => 999
        ]);

        Notification::assertSentTo(auth()->user(),ThreadWasUpdated::class);
    }   

    /** @test */
    public function a_thread_belongs_to_a_channel()
    {
        $thread = create('App\Thread');

        $this->assertInstanceOf('App\Channel',$thread->channel);
    }

    /** @test */
    function it_knows_the_authenticated_user_is_subscribed_to_it()
    {
        $thread = create('App\Thread');

        $this->signIn();

        $this->assertFalse($thread->isSubscribedTo);

        $thread->subscribe();

        $this->assertTrue($thread->isSubscribedTo);
    }

    /** @test */
    function a_thread_can_check_if_the_authenticated_user_has_read_all_reply()
    {
        $this->signIn();

        $thread = create('App\Thread');
        tap(auth()->user(),function($user) use($thread){
            $this->assertTrue($thread->hasUpdatesFor($user));

            $user->read($thread);

            $this->assertFalse($thread->hasUpdatesFor($user));

        });
        
    }
    //for redis visits count
    function a_thread_records_each_visit()
    {
        $thread = make('App\Thread',['id'=>1]);

        $thread->visits()->reset();
        
        $this->assertSame(0,$thread->visits()->count());
        
        $thread->visits()->record();

        $this->assertEquals(1,$thread->visits()->count());
        
        $thread->visits()->record();

        $this->assertEquals(2,$thread->visits()->count());       
    }

    /** @test */
    function we_record_a_new_visit_each_time_the_thread_is_read()
    {
        $thread = create('App\Thread');

        $this->assertSame(0,$thread->visits);

        $this->call('GET',$thread->path());

        $this->assertEquals(1,$thread->fresh()->visits);
    }

    /** @test */
    function a_threads_body_is_sanitized_automatically()
    {
        $thread = make('App\Thread',['body'=>'<script>alert("bad")</script>']);

        $this->assertEmpty($thread->body);
    }
}
