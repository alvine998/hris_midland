<x-mail::message>
# Your {{ config('app.name') }} Account is Ready

Hello **{{ $companyName }}**,

Your order has been approved and your workspace is now active. Below are your login credentials:

<x-mail::panel>
**Email:** {{ $email }}<br>
**Password:** {{ $password }}
</x-mail::panel>

Please change your password after your first login.

<x-mail::button :url="route('login')">
Log in to Your Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }} Team
</x-mail::message>
