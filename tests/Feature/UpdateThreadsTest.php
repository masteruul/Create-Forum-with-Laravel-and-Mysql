<?php
namespace Tests\Feature;
use App\Activity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    use RefreshDatabase;

    function setUp()
    {
        parent::setUp();

        $this->withExceptionHandling();

        $this->signIn();
    }

    /** @test */
    function a_thread_requires_a_title_and_body_to_be_updated()
    {
        $thread = create('App\Thread',['user_id'=>auth()->id()]);

        $this->patch($thread->path(),[
            'title' => 'Changed'
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(),[
            'body' => 'Changed'
        ])->assertSessionHasErrors('title');

    }
    /** @test */
    function unauthorized_users_may_not_update_threads()
    {   
        $thread = create('App\Thread');

        $this->patch($thread->path(),[
            'title' => 'Changed'
        ])->assertStatus(403);
    }

    /** @test */
    function a_thread_can_be_updated()
    {
        $thread = create('App\Thread',['user_id'=>auth()->id()]);

        $this->patch($thread->path(),[
            'title' => 'Changed',
            'body' => 'Changed body.'
        ]);
        tap($thread->fresh(),function($thread){
            $this->assertEquals('Changed',$thread->title);
            $this->assertEquals('Changed body.',$thread->body);
        });
    }

}