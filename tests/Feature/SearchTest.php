<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Thread;

class SearchTest extends TestCase
{
    use RefreshDatabase;


    /** @test */
    public function a_user_can_search_threads()
    {
        config(['scout.driver' => 'algolia']);
        $search = 'foobar';

        create('App\Thread',[],2);
        $desiredSearch = create('App\Thread',['body'=>'A thread have {$search} term.'],2);

        $results = $this->getJson("/threads/search?1={$search}")->json();
        //result should be different depend on search index.
        $this->assertCount(1,$results['data']);
        
        $desiredSearch->unsearchable();
    } 
}
