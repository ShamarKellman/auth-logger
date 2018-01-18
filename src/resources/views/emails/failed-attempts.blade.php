@component('mail::message')
    # Hello!

    Your {{ config('app.name') }} has received multiple failed login attempts.

    > **Account:** {{ $account->email }}<br>
    > **Time:** {{ $time->toCookieString() }}<br>
    > **IP Address:** {{ $ipAddress }}<br>
    > **Location:** {{ $location }}
    > **Browser:** {{ $browser }}

    If this was you, you can ignore this alert. If you suspect any suspicious activity on your account, please change your password immediately.

    Regards,<br>{{ config('app.name') }}
@endcomponent