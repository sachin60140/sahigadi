<x-mail::message>
# RC lookups are failing

Attestr is refusing our requests, so every vehicle RC lookup on {{ config('app.name') }} is failing until this is fixed. Dealers and customers are being told the service is temporarily unavailable, and nobody is being charged.

**Attestr error code:** {{ $code }}

{{ $summary }}

**First seen:** {{ $firstSeen }}

This alert is sent at most once an hour for each error code, so further failures in the next hour will not send another email.

Sign in to the Attestr console to check the account, its credit balance and the History tab.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
