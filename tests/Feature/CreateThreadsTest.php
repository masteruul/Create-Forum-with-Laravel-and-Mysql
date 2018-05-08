<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;


    /** @test */
    function guest_may_not_create_threads()
    {
        $this->withExceptionHandling();

        $this->get('/threads/create')
            ->assertRedirect('/login');

        $this->post('/threads')
            ->assertRedirect('/login');
    }


    /** @test */
    function an_authenticated_user_can_create_new_forum_threads()
    {
        //given signed user
        $this->signIn();

        //when we hit the endpoint to create new thread
        $thread=make('App\Thread');

        $response = $this->post('/threads',$thread->toArray());

        //then, when we visit the thread page
        $this->get($response->headers->get('Location'))
            ->assertSee($thread->title)
            ->assertSee($thread->body);
    }

    /** @test */
    function a_thread_required_a_title(){

        $this->publishThread(['title'=>null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    function a_thread_required_a_body(){

    $this->publishThread(['body'=>null])
        ->assertSessionHasErrors('body');
    }

    /** @test */
    function a_thread_required_a_valid_channel(){
        factory('App\Channel',2)->create();

        $this->publishThread(['channel_id'=>null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id'=>999])
            ->assertSessionHasErrors('channel_id');    
    }


    public function publishThread($overrides=[]){

        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread',$overrides);

        return $this->post('/threads',$thread->toArray());
    }
}
