<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with('wallet');

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customer_unique_id', 'like', '%' . $request->search . '%')
                    ->orWhere('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%')
                    ->orWhere('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('company_name', 'like', '%' . $request->search . '%');
            });
        }

        $customers = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $customer->load(['wallet.transactions' => function($q) {
            $q->orderBy('created_at', 'desc')->limit(20);
        }, 'listings']);

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
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
        ]);

        $customer->update($request->all());

        try {
            \Illuminate\Support\Facades\Mail::to($customer->email)
                ->send(new \App\Mail\CustomerProfileUpdatedByAdmin($customer));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send customer profile update email: ' . $e->getMessage());
            return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer updated successfully, but failed to send email notification.');
        }

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer profile updated and notification sent successfully.');
    }
}
