<x-mail::message>
# {{ $details['is_admin'] ? 'New Featured Plan Subscription' : 'Featured Plan Subscribed Successfully' }}

Hi {{ $details['user_name'] }},

@if($details['is_admin'])
A user has just purchased a featured plan for their car listing.
@else
You have successfully subscribed to a featured plan for your car. This will boost your visibility on our homepage and listing pages!
@endif

<x-mail::panel>
**Car Details:**
* **ID:** #{{ $details['car_id'] }}
* **Title:** {{ $details['car_title'] }}

**Plan Details:**
* **Plan Name:** {{ $details['plan_name'] }} ({{ $details['duration_days'] }} Days)
* **Amount Paid:** ₹{{ number_format($details['amount_paid'], 2) }}
* **Valid Until:** {{ \Carbon\Carbon::parse($details['expires_at'])->format('d M Y, h:i A') }}
</x-mail::panel>

@if(!$details['is_admin'])
<x-mail::button :url="$details['action_url']">
View Dashboard
</x-mail::button>
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
