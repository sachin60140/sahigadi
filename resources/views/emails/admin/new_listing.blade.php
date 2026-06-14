<x-mail::message>
# New Car Listing Requires Approval

A new car listing has been submitted and is waiting for your review.

**Title:** {{ $car->title }}
**Listed By:** {{ $isDealer ? 'Dealer (' . $car->dealer->name . ')' : 'Customer (' . $car->owner_name . ')' }}
**Price:** ₹{{ number_format($car->price ?? 0) }}
**City:** {{ $car->city ?? 'N/A' }}

Please log in to the admin panel to review, approve, or reject this listing.

<x-mail::button :url="route('admin.cars.index')">
View Listings
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>

