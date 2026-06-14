<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('wallet')->withCount('listings');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_unique_id', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('company_name', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers->through(fn ($customer) => $this->mapCustomerList($customer)),
            'filters' => [
                'search' => $request->query('search', ''),
            ],
            'stats' => [
                'total' => Customer::count(),
                'complete_profiles' => Customer::where('profile_completion_percentage', '>=', 75)->count(),
                'with_listings' => Customer::has('listings')->count(),
            ],
        ]);
    }

    public function show(Customer $customer)
    {
        $customer->load(['wallet.transactions' => function($q) {
            $q->orderBy('created_at', 'desc')->limit(20);
        }, 'listings.brand']);

        return Inertia::render('Admin/Customers/Show', [
            'customer' => $this->mapCustomerDetail($customer),
        ]);
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Admin/Customers/Edit', [
            'customer' => $this->mapCustomerForm($customer),
            'actions' => [
                'update' => route('admin.customers.update', $customer),
                'back' => route('admin.customers.show', $customer),
            ],
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id,
            'phone' => 'required|string|max:20|unique:customers,phone,' . $customer->id,
            'whatsapp_number' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:20',
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
        ]);

        $customer->update($validated);
        $customer->calculateProfileCompletion();

        try {
            \Illuminate\Support\Facades\Mail::to($customer->email)
                ->send(new \App\Mail\CustomerProfileUpdatedByAdmin($customer));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send customer profile update email: ' . $e->getMessage());
            return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer updated successfully, but failed to send email notification.');
        }

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer profile updated and notification sent successfully.');
    }

    private function mapCustomerList(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'customer_unique_id' => $customer->customer_unique_id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'whatsapp_number' => $customer->whatsapp_number,
            'company_name' => $customer->company_name,
            'city' => $customer->city,
            'state' => $customer->state,
            'wallet_balance' => (float) ($customer->wallet?->balance ?? 0),
            'profile_completion_percentage' => (int) ($customer->profile_completion_percentage ?? 0),
            'listings_count' => (int) ($customer->listings_count ?? 0),
            'joined_at' => $this->formatDate($customer->created_at),
            'show_url' => route('admin.customers.show', $customer),
            'edit_url' => route('admin.customers.edit', $customer),
        ];
    }

    private function mapCustomerDetail(Customer $customer): array
    {
        return [
            ...$this->mapCustomerList($customer),
            'address' => $customer->address,
            'pincode' => $customer->pincode,
            'gst_number' => $customer->gst_number,
            'aadhaar_number' => $customer->aadhaar_number,
            'pan_number' => $customer->pan_number,
            'gender' => $customer->gender,
            'dob' => $this->formatDate($customer->dob),
            'missing_profile_fields' => $customer->getMissingProfileFields(),
            'listings' => $customer->listings->map(fn ($listing) => [
                'id' => $listing->id,
                'title' => trim(($listing->brand?->name ? $listing->brand->name.' ' : '').($listing->model ?: $listing->title)),
                'model' => $listing->model,
                'brand' => $listing->brand?->name,
                'year' => $listing->year,
                'fuel_type' => $listing->fuel_type,
                'transmission' => $listing->transmission,
                'km_driven' => $listing->km_driven,
                'price' => (float) ($listing->price ?? 0),
                'city' => $listing->city,
                'status' => $listing->status,
                'is_featured' => (bool) $listing->is_featured,
            ])->values(),
            'wallet_transactions' => $customer->wallet?->transactions?->map(fn ($transaction) => [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => (float) $transaction->amount,
                'remark' => $transaction->remark,
                'reference_id' => $transaction->reference_id,
                'reference_type' => $transaction->reference_type,
                'created_at' => $this->formatDateTime($transaction->created_at),
            ])->values() ?? [],
            'actions' => [
                'back' => route('admin.customers.index'),
                'edit' => route('admin.customers.edit', $customer),
            ],
        ];
    }

    private function mapCustomerForm(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'customer_unique_id' => $customer->customer_unique_id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'whatsapp_number' => $customer->whatsapp_number,
            'company_name' => $customer->company_name,
            'gst_number' => $customer->gst_number,
            'address' => $customer->address,
            'city' => $customer->city,
            'state' => $customer->state,
            'pincode' => $customer->pincode,
            'aadhaar_number' => $customer->aadhaar_number,
            'pan_number' => $customer->pan_number,
            'gender' => $customer->gender,
            'dob' => $customer->dob?->format('Y-m-d'),
            'profile_completion_percentage' => (int) ($customer->profile_completion_percentage ?? 0),
            'missing_profile_fields' => $customer->getMissingProfileFields(),
        ];
    }

    private function formatDate($value): ?string
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('d M Y');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y');
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function formatDateTime($value): ?string
    {
        if (! $value) {
            return null;
        }

        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('d M Y, h:i A');
        }

        try {
            return \Carbon\Carbon::parse($value)->format('d M Y, h:i A');
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
