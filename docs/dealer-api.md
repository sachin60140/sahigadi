# SahiGadi Dealer API — Vehicle (RC) Lookup

Server-to-server API for approved SahiGadi dealers to run Vahan/RTO registration
lookups from their own systems. Each successful lookup is charged to the dealer's
SahiGadi wallet.

- **Base URL:** `https://sahigadi.com/api/v1`
- **Auth:** Bearer token (Laravel Sanctum)
- **Format:** JSON
- **Rate limit:** 60 requests/minute per dealer

---

## 1. Getting an API key

1. Log in to the dealer panel at `https://sahigadi.com/dealer/login`
2. Go to **API Access** in the sidebar
3. Click **Generate API key**

The key is shown **once**. Copy it immediately and store it in your application's
secret store (`.env`, secrets manager) — never in source control.

Notes:
- A dealer holds **one active key**. Regenerating immediately invalidates the previous one.
- Your key is separate from any mobile-app session; revoking one does not affect the other.
- API access requires an **approved** dealer account with API access enabled by SahiGadi.

---

## 2. Required headers

```
Authorization: Bearer <your-api-key>
Accept: application/json
Content-Type: application/json      # POST requests only
```

> **`Accept: application/json` is mandatory.** Without it the API is treated as a
> browser request and authentication failures return a **302 redirect to the login
> page** instead of a JSON `401`. This is the single most common integration mistake.

---

## 3. Endpoints

| Method | Path | Billed | Description |
|---|---|---|---|
| `POST` | `/vehicle/rc` | **Yes** | Look up registration details |
| `GET` | `/account/balance` | No | Wallet balance and current price |
| `GET` | `/vehicle/searches` | No | Your lookup history (paginated) |
| `GET` | `/vehicle/rc/{id}` | No | Re-fetch a previous result |

### POST /vehicle/rc

**Request**
```json
{ "registration_number": "BR06CQ2725" }
```

`registration_number` — required, 4–20 chars. Case and separators are normalised
server-side, so `dl2caz3861`, `DL2C AZ-3861` and `DL2CAZ3861` are equivalent.

**Response `200`**
```json
{
  "success": true,
  "cached": false,
  "charged": 25,
  "wallet_balance": 5901,
  "message": "Vehicle details retrieved successfully.",
  "data": {
    "id": 12,
    "registration_number": "BR06CQ2725",
    "is_success": true,
    "searched_at": "2026-08-01T10:14:02+05:30",
    "details": {
      "status": "ACTIVE",
      "registered": "10-04-2021",
      "owner": "BIBHESH KUMAR",
      "ownerNumber": 1,
      "currentAddress": "...",
      "makerDescription": "MARUTI SUZUKI INDIA LTD",
      "makerModel": "BALENO ZETA PETROL",
      "chassisNumber": "MBHEWB22SMC665910",
      "engineNumber": "K12MP4194083",
      "fuelType": "PETROL",
      "colorType": "PEARL ARCTIC WHITE",
      "normsType": "BHARAT STAGE VI",
      "insuranceProvider": "...",
      "insuranceUpto": "07-04-2027",
      "fitnessUpto": "09-04-2036",
      "pollutionCertificateUpto": "11-04-2026",
      "rto": "MUZAFFARPUR, BIHAR",
      "financed": "1",
      "lender": "HDFC BANK",
      "cubicCapacity": "1197.00",
      "seatingCapacity": 5
    }
  }
}
```

`data.details` is the provider payload passed through as-is (~60 keys). Fields not
returned for a given vehicle are empty strings. **Treat every key as optional** —
do not assume presence.

### GET /account/balance

```json
{
  "success": true,
  "data": { "wallet_balance": 5901, "charge_per_search": 25, "currency": "INR" }
}
```

Call this before batch jobs to confirm you have enough balance.

### GET /vehicle/searches

Paginated history. Optional `?per_page=` (default 20, max 100).

```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "total": 37,
    "data": [
      { "id": 12, "registration_number": "BR06CQ2725", "is_success": true,
        "searched_at": "2026-08-01T10:14:02+05:30" }
    ]
  }
}
```

### GET /vehicle/rc/{id}

Re-fetch a stored result by its numeric `id`, free of charge. Scoped to your own
records — another dealer's `id` returns `404`.

---

## 4. Status codes

| Code | Meaning | Action |
|---|---|---|
| `200` | Success | — |
| `401` | Missing or invalid key | Check the key and the `Authorization` header |
| `402` | **Insufficient wallet balance** | Recharge. No lookup ran; nothing was charged |
| `403` | API not enabled for your account, or a non-API token was used | Contact SahiGadi support |
| `422` | Invalid `registration_number` | Fix the input |
| `429` | Rate limit exceeded (60/min) | Back off and retry |
| `503` | API globally disabled | Temporary; retry later |

Error shape:
```json
{ "success": false, "message": "Insufficient wallet balance. Required: ₹25.00", "data": null }
```

---

## 5. Billing

- **₹25 per successful lookup**, debited from the dealer wallet (confirm the current
  figure via `/account/balance`).
- **Repeat lookups of the same vehicle within 24 hours are free** — the response
  carries `"cached": true, "charged": 0`.
- **Failed lookups are not charged.** If the provider errors or the vehicle is not
  found, nothing is deducted.
- **`402` performs no lookup**, so a low balance never partially charges you.

The 24-hour cache also makes retries safe: re-sending a request after a network
timeout returns the cached result at no cost, so there is no double-charge risk.

---

## 6. Integration notes

**Retry policy.** Retry on `429` (respect `Retry-After` if present) and on 5xx.
Do **not** blindly retry `402` or `422` — they will keep failing.

**Batch jobs.** At 60 requests/minute, space calls ~1s apart. Check
`/account/balance` first: a 200-vehicle run at ₹25 needs ₹5,000 available.

**Storing results.** `data.id` is stable — persist it so you can re-fetch a report
later via `/vehicle/rc/{id}` without paying again.

**Timeouts.** Upstream RTO lookups can take several seconds. Use a client timeout
of at least 60s for `POST /vehicle/rc`.

**Key rotation.** Regenerating in the dealer panel invalidates the old key
instantly. Deploy the new key before regenerating to avoid downtime.

---

## 7. Examples

### cURL
```bash
curl -X POST https://sahigadi.com/api/v1/vehicle/rc \
  -H "Authorization: Bearer $SAHIGADI_API_KEY" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"registration_number":"BR06CQ2725"}'
```

### PHP (Laravel)
```php
use Illuminate\Support\Facades\Http;

$response = Http::withToken(config('services.sahigadi.key'))
    ->acceptJson()
    ->timeout(60)
    ->post('https://sahigadi.com/api/v1/vehicle/rc', [
        'registration_number' => $registrationNumber,
    ]);

if ($response->status() === 402) {
    throw new RuntimeException('SahiGadi wallet balance is too low.');
}

if (! $response->successful()) {
    throw new RuntimeException('Lookup failed: '.$response->json('message'));
}

$details = $response->json('data.details');
$wasFree = $response->json('cached');
```

### Node (fetch)
```js
const res = await fetch('https://sahigadi.com/api/v1/vehicle/rc', {
  method: 'POST',
  headers: {
    Authorization: `Bearer ${process.env.SAHIGADI_API_KEY}`,
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({ registration_number: 'BR06CQ2725' }),
});

if (res.status === 402) throw new Error('Wallet balance too low');
if (!res.ok) throw new Error(`Lookup failed: ${res.status}`);

const { data, cached, charged } = await res.json();
```

---

## 8. Support

Usage and billing for every API call are visible to SahiGadi admins under
**RC Search → API usage**. If calls fail unexpectedly, quote the registration
number and approximate timestamp.
