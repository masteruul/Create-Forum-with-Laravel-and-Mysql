@component('mail::message')
# One Last Step

Kami ingin memastikan apakah anda manusia atau hantu yang penasaran dengan internet.

@component('mail::button', ['url' => url('/register/confirm?token='.$user->confirmation_token)])
Ya, Saya Manusia !!
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
