<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;


class ReplyTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function it_has_an_owner()
    {
        $reply = factory('App\Reply')->create();
        $this->assertInstanceOf('App\User',$reply->owner);
    }

    /** @test */
    function it_knows_if_it_was_just_published()
    {
        $reply = factory('App\Reply')->create();
        $this->assertTrue($reply->wasJustPublished());
        
        $reply->created_at = Carbon::now()->subMonth();
        $this->assertFalse($reply->wasJustPublished());
        
    }

    /** @test */
    function it_can_detect_all_mentioned_users_in_the_body()
    {
        $reply = new \App\Reply([
            'body' => '@susu wants to talk to @sasa'
        ]);

        $this->assertEquals(['susu','sasa'], $reply->mentionedUsers());
    }

    /** @test */
    function it_wraps_mentioned_username_in_the_body_within_anchor_tags()
    {
        $reply =new \App\Reply([
            'body' => 'Hello @sususu.'
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/sususu">@sususu</a>.',
            $reply->body
        );
    }
}
