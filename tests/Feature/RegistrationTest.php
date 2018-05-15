<?php

namespace Tests\Feature;

use App\User;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class RegistrationTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();

        $this->post(route('register'),[
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'john111',
            'password_confirmation' => 'john111'
        ]);

        Mail::assertSent(PleaseConfirmYourEmail::class);
    }

    /** @test */
    function user_can_fully_confirm_their_email_addresses()
    {
        Mail::fake();
        $this->post(route('register'),[
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'john111',
            'password_confirmation' => 'john111'
        ]);

        $user = User::whereName('John')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);        
            
        //Let the user confirm their account
        $this->get(route('register.confirm',['token'=>$user->confirmation_token]))
            ->assertRedirect(route('threads'));
        $this->assertTrue($user->fresh()->confirmed);
    }
     /** @test */
    function confirming_an_invalid_token()
    {
        $this->get(route('register.confirm', ['token' => 'invalid']))
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'Unknown token.');
    }
}