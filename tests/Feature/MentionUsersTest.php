<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class MentionUsersTest extends TestCase
{
    use DatabaseMigrations;
    
    /** @test */
    function mentioned_users_in_a_reply_area_notified()
    {
        $susu = create('App\User',['name'=>'susu']);

        $this->signIn($susu);

        $sasa = create('App\User',['name'=>'sasa']);

        $thread = create('App\Thread');

        $reply = make('App\Reply',[
            'body' => '@sasa look at this'
        ]);

        $this->json('post',$thread->path().'/replies',$reply->toArray());

        $this->assertCount(1,$sasa->notifications);
    }

    /** @test */
    function it_can_fetch_all_mentioned_users_starting_with_the_given_characters()
    {
        create('App\User',['name'=>'johndoe']);
        create('App\User',['name'=>'johndoe2']);
        create('App\User',['name'=>'janendoe']);
        
        $results = $this->json('GET','/api/users',['name'=>'john']);

        $this->assertCount(2,$results->json());
    }
}
