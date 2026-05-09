<x-mail::message>
# {{ $details['is_admin'] ? 'Featured Plan Expiring Soon' : 'Your Featured Plan is Expiring' }}

Hi {{ $details['user_name'] }},

@if($details['is_admin'])
The featured plan for the following car is expiring within 24 hours.
@else
This is a gentle reminder that your featured plan for the following car will expire within 24 hours. To maintain your premium visibility, please renew your plan.
@endif

<x-mail::panel>
**Car Details:**
* **ID:** #{{ $details['car_id'] }}
* **Title:** {{ $details['car_title'] }}
* **Expires At:** {{ \Carbon\Carbon::parse($details['expires_at'])->format('d M Y, h:i A') }}
</x-mail::panel>

@if(!$details['is_admin'])
<x-mail::button :url="$details['action_url']">
Renew Plan
</x-mail::button>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
