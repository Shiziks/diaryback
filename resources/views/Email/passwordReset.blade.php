@component('mail::message')
# Hello There!

We've received a request for password reset. To continue please click on the button bellow.

@component('mail::button', ['url' => 'http://localhost:4200/resetpasswordform?token='.$token.'&email='.$email])
Reset Password
@endcomponent

If this wasn't you, just ignore this email.

Thanks,<br>
MeLog team
@endcomponent
