# Attestr DPDP V3 migration — what is known, and what still blocks it

Attestr's console carries a banner: **"Mandatory Transition to DPDPA Compliant V3
APIs by 01 Oct 2026"**. Only the RC lookup uses Attestr — challan and all four
service-history integrations call `api.invincibleocean.com` and are unaffected.

**v2 is still serving.** Successful lookups went through on 01 Oct 2026, the stated
transition date, so the endpoint was not cut off at the deadline. The migration is
needed, but the feature is not down.

## What is specified

Version strings, from the console's version selector:

| | |
|---|---|
| `v3` | "Latest (DPDPA Compliant) Version" |
| `v2` | "Old Version" |

So the endpoint becomes `https://api.attestr.com/api/v3/public/checkx/rc`.

### Register Consent

`POST https://api.attestr.com/api/v3/public/consent/register`

```json
{
    "consentPurpose": "kyc_verification|background_verification",
    "consentPurposeDesc": "[optional]",
    "services": [{ "service": "[service code]", "options": {} }],
    "consentDataCategories": [{ "category": "[category]", "values": ["[type]"] }],
    "consentType": "single_use|multi_use",
    "consentMode": "checkbox|email_otp|mobile_otp|digilocker|ivr|physical_form|offline",
    "consentTimestamp": "[ISO]",
    "consentValidFrom": "[ISO]",
    "consentValidTill": "[ISO]",
    "consentReferenceId": "[our unique reference]",
    "consentEvidenceRef": "[required proof reference]",
    "consentOperations": ["VERIFY|FETCH|EXPORT|STORE|SHARE|REPORT"],
    "clientDeclaration": true,
    "webhook": true
}
```

Response:

```json
{ "_id": "[consent id]", "number": "[human-readable number]" }
```

The docs state the `_id` "must subsequently be passed in all related verification
and KYC API requests to establish a valid consent trail".

### Definitions that matter for our design

- **`single_use`** — "Once a Single Use Consent has been consumed in a successful
  operation, the associated consent becomes invalid and cannot be reused."
- **`multi_use`** — validity-based; "remain valid until their configured expiry or
  revocation".
- **`consentOperations`** — VERIFY (real-time verification), FETCH (retrieve data),
  STORE (retain for future use), REPORT (**"including PDF downloads, portal-based
  views"**), EXPORT (bulk/batch), SHARE (with authorised third parties).

We serve a cached result for 24 hours and offer a PDF download, so STORE and
REPORT are both in scope, not just VERIFY and FETCH.

## What blocks the integration

**The request field that carries the consent id on a product endpoint is not
published anywhere.** Checked and absent from:

- `docs.attestr.com/attestr-docs/vehicle-rc-check-api` (HTML)
- the same page at the `/v3/` path
- `…/vehicle-rc-check-api.md`, the canonical source
- `…/attestr-dpdpa-implementation.md`
- `…/bank-account-verification-api` — which the migration guide itself uses as its
  worked example, and which still shows only `acc` / `ifsc` / `fetchIfsc`
- the `llms.txt` index

**No service code is published** for Vehicle RC Check, which Register Consent needs
in `services[].service`.

**No consent purpose fits.** Only `kyc_verification` and `background_verification`
exist. A marketplace vehicle check is neither, and processing beyond the declared
purpose is unauthorised.

The console's **History → View Details** on a v3 request is the one place the real
request shape can be seen. That is the fastest route to the field name.

## The product question, which outranks the code

For an RC lookup the Data Principal is the **registered owner**, not the dealer
searching. Every `consentMode` Attestr supports presupposes someone you can already
reach — and the owner's mobile is itself a field the lookup returns, so they cannot
be OTP'd before the first call.

| Flow | Under a consent model |
|---|---|
| Owner looking up their own vehicle | Works — they are the Data Principal |
| Dealer with the owner present | Works — OTP to the owner's own handset |
| Dealer or buyer typing an arbitrary plate | **No documented lawful path** |

The third case is most of what the paid lookup sells today, including the
pass-through documented in `docs/dealer-api.md`, which promises integrators the
provider payload "as-is (~60 keys)" with `owner` and `currentAddress` in the
published sample. Narrowing that is a breaking change to a paid contract.

The highest-value open question is whether a **specification-only** mode exists —
make, model, fuel, insurance and fitness validity, blacklist, finance, RTO, with no
owner PII — since that needs no consent from the owner and is most of what a buyer
actually wants.

## How the code is prepared

`App\Services\Attestr\AttestrRcClient` is now the only place the RC request is
built; both `VehicleSearchService` and `CustomerVehicleSearchService` call it. The
switch is configuration:

```
VEHICLE_API_URL=https://api.attestr.com/api/v3/public/checkx/rc
VEHICLE_API_VERSION=v3
VEHICLE_API_CONSENT_FIELD=<the name Attestr confirms>
```

Setting `VEHICLE_API_VERSION=v3` without `VEHICLE_API_CONSENT_FIELD` throws with a
message naming what is missing, rather than sending a request Attestr will reject.
Still to build once the above is answered: consent registration, storage of the
returned `_id`, and an owner-side consent capture flow.
