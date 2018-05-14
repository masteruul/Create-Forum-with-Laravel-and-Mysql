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
}
