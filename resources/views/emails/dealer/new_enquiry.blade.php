<x-mail::message>
# You have a new Lead!

Hi {{ $enquiry->dealer->name ?? 'Dealer' }},

Great news! A potential buyer just viewed the contact details for one of your car listings on SAHIGADI.

**Car Details:**
- **Title:** {{ $enquiry->actual_car->title ?? 'N/A' }}
- **Price:** ₹{{ number_format($enquiry->actual_car->price ?? 0) }}

**Customer Information:**
- **Name:** {{ $enquiry->customer_name }}
- **Phone:** {{ $enquiry->customer_phone }}
- **Date:** {{ $enquiry->created_at->format('d M Y, h:i A') }}

Please reach out to the customer as soon as possible to close the deal!

<x-mail::button :url="route('dealer.dashboard')">
View in Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
