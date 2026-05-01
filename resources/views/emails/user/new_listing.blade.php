<x-mail::message>
# Your Car Listing is Received!

Hi there,

Thank you for listing your car on SAHI GADI. Your new listing has been successfully submitted and is currently **Pending Approval**.

**Car Details:**
- **Title:** {{ $car->title }}
- **Price:** ₹{{ number_format($car->price ?? 0) }}
- **Registration:** {{ substr($car->registration_number ?? 'XXXX', 0, 4) }}****

Our team will review your listing shortly. You will be notified once it goes live on the platform!

<x-mail::button :url="route('home')">
Visit SAHI GADI
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
