<?php 

namespace Tests\Feature;

use Tests\TestCase;
use App\Inspections\Spam;

class SpamTest extends TestCase
{
    /** @test */
    public function it_validation_spam()
    {
        $spam = new Spam();
        $this->assertFalse($spam->detect('Innocent Reply Here'));
    }

    /** @test */
    public function it_checks_for_any_key_being_held_down()
    {
        $spam = new Spam();
        $this->expectException('Exception');
        
        $spam->detect('Hello World aaaaaaaa');
    }
}