<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Customer;
use App\Models\CustomerCarListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function showLogin()
    {
        return Inertia::render('Auth/CustomerLogin', [
            'actions' => [
                'sendOtp' => route('customer.send-otp'),
                'verifyOtp' => route('customer.verify-otp'),
            ],
        ]);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
        ]);

        $phone = $request->phone;
        $otp = random_int(100000, 999999);
        
        session(['customer_login_otp_' . $phone => $otp]);
        session(['customer_login_otp_time_' . $phone => now()]);

        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";
        
        Http::get($apiUrl, [
            'username' => config('services.smartping.username'),
            'password' => config('services.smartping.password'),
            'unicode' => 'false',
            'from' => 'INSARS',
            'text' => $text,
            'to' => $phone,
            'dltContentId' => '1707177677498830200',
            'dltPrincipalEntityId' => '1701166126846262605'
        ]);

        $customer = Customer::where('phone', $phone)->first();
        if ($customer && $customer->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\CustomerOtpMail($otp, 'login'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send login OTP email: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
            'otp' => 'required|numeric|digits:6',
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $sessionOtp = session('customer_login_otp_' . $phone);
        $sessionTime = session('customer_login_otp_time_' . $phone);

        if (!$sessionOtp || !$sessionTime) {
            return response()->json(['success' => false, 'message' => 'OTP expired or not sent']);
        }

        if (now()->diffInMinutes($sessionTime) > 10) {
            return response()->json(['success' => false, 'message' => 'OTP has expired']);
        }

        if ((string)$sessionOtp === (string)$otp) {
            session()->forget('customer_login_otp_' . $phone);
            session()->forget('customer_login_otp_time_' . $phone);
            
            $customer = Customer::firstOrCreate(
                ['phone' => $phone]
            );

            if (empty($customer->name)) {
                $latestListing = CustomerCarListing::where('owner_phone', $phone)->whereNotNull('owner_name')->latest()->first();
                if ($latestListing) {
                    $customer->name = $latestListing->owner_name;
                    $customer->save();
                }
            }

            Auth::guard('customer')->login($customer);
            
            if ($customer->profile_completion_percentage === 0) {
                $customer->calculateProfileCompletion();
            }

            if ($customer->profile_completion_percentage < 75) {
                return response()->json(['success' => true, 'message' => 'Logged in successfully. Please complete your profile.', 'redirect' => route('customer.profile.edit')]);
            }
            
            return response()->json(['success' => true, 'message' => 'Logged in successfully', 'redirect' => route('customer.dashboard')]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid OTP']);
    }

    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        
        // Find all listings associated with this phone number
        $listings = CustomerCarListing::with('brand')
            ->where('owner_phone', $customer->phone)
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Customer/Dashboard', [
            'customer' => $this->mapCustomer($customer),
            'listings' => $listings->map(fn (CustomerCarListing $listing) => $this->mapListing($listing)),
            'stats' => [
                'total' => $listings->count(),
                'approved' => $listings->where('status', 'approved')->count(),
                'pending' => $listings->where('status', 'pending')->count(),
                'featured' => $listings->filter(fn (CustomerCarListing $listing) => $listing->isFeatured())->count(),
            ],
            'actions' => [
                'sellCar' => route('sell-car.index'),
                'sendDeleteOtp' => route('customer.listing.delete.otp'),
            ],
        ]);
    }

    public function editProfile()
    {
        $customer = Auth::guard('customer')->user();

        if ((int) $customer->profile_completion_percentage === 0) {
            $customer->calculateProfileCompletion();
            $customer->refresh();
        }

        return Inertia::render('Customer/Profile/Edit', [
            'customer' => [
                ...$this->mapCustomer($customer),
                'whatsapp_number' => $customer->whatsapp_number,
                'address' => $customer->address,
                'city' => $customer->city,
                'state' => $customer->state,
                'pincode' => $customer->pincode,
                'gst_number' => $customer->gst_number,
                'company_name' => $customer->company_name,
                'aadhaar_number' => $customer->aadhaar_number,
                'pan_number' => $customer->pan_number,
                'gender' => $customer->gender,
                'dob' => optional($customer->dob)->format('Y-m-d'),
                'missing_fields' => $customer->getMissingProfileFields(),
            ],
            'actions' => [
                'update' => route('customer.profile.update'),
                'dashboard' => route('customer.dashboard'),
            ],
        ]);
    }

    public function updateProfile(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'gst_number' => 'nullable|string|max:15',
            'company_name' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'aadhaar_number' => 'nullable|string|max:20',
            'pan_number' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'whatsapp_number' => $request->whatsapp_number,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'gst_number' => $request->gst_number,
            'company_name' => $request->company_name,
            'aadhaar_number' => $request->aadhaar_number,
            'pan_number' => $request->pan_number,
            'gender' => $request->gender,
            'dob' => $request->dob,
        ];

        if ($request->hasFile('profile_image')) {
            if ($customer->profile_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($customer->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('customer-profiles', 'public');
        }

        $customer->update($data);
        $customer->calculateProfileCompletion();

        // Send Email Notification
        if ($customer->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\CustomerProfileUpdated($customer));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send customer self profile update email: ' . $e->getMessage());
            }
        }

        if ($customer->profile_completion_percentage < 75) {
            return redirect()->route('customer.profile.edit')->with('error', 'Profile updated, but completion is still below 75%. Please fill missing fields.');
        }

        return redirect()->route('customer.dashboard')->with('success', 'Profile updated successfully.');
    }

    public function sendDeleteOtp()
    {
        $customer = Auth::guard('customer')->user();
        $phone = $customer->phone;
        $otp = random_int(100000, 999999);
        
        session(['customer_delete_otp_' . $phone => $otp]);
        session(['customer_delete_otp_time_' . $phone => now()]);

        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";
        
        Http::get($apiUrl, [
            'username' => config('services.smartping.username'),
            'password' => config('services.smartping.password'),
            'unicode' => 'false',
            'from' => 'INSARS',
            'text' => $text,
            'to' => $phone,
            'dltContentId' => '1707177677498830200',
            'dltPrincipalEntityId' => '1701166126846262605'
        ]);

        if ($customer->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\CustomerOtpMail($otp, 'listing deletion'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send deletion OTP email: ' . $e->getMessage());
            }
        }

        return response()->json(['success' => true, 'message' => 'OTP sent successfully to your registered mobile number' . ($customer->email ? ' and email.' : '.')]);
    }

    public function deleteListing(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();
        $phone = $customer->phone;

        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $sessionOtp = session('customer_delete_otp_' . $phone);
        $sessionTime = session('customer_delete_otp_time_' . $phone);

        if (!$sessionOtp || !$sessionTime) {
            return response()->json(['success' => false, 'message' => 'OTP expired or not sent']);
        }

        if (now()->diffInMinutes($sessionTime) > 10) {
            return response()->json(['success' => false, 'message' => 'OTP has expired']);
        }

        if ((string)$sessionOtp !== (string)$request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid OTP']);
        }

        session()->forget('customer_delete_otp_' . $phone);
        session()->forget('customer_delete_otp_time_' . $phone);

        $listing = CustomerCarListing::where('id', $id)
            ->where('owner_phone', $customer->phone)
            ->firstOrFail();

        // Optionally delete images from storage
        $images = json_decode($listing->images ?? '[]', true);
        foreach ($images as $img) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($img);
        }

        $email = $listing->owner_email ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('email');
        if ($email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\UserListingStatusNotification($listing, 'deleted'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send deletion email: ' . $e->getMessage());
            }
        }

        $listing->delete();

        return response()->json(['success' => true, 'message' => 'Listing deleted successfully.']);
    }

    public function editListing($id)
    {
        $customer = Auth::guard('customer')->user();
        $listing = CustomerCarListing::where('id', $id)
            ->where('owner_phone', $customer->phone)
            ->firstOrFail();

        if ($listing->status === 'approved') {
            return redirect()->route('customer.dashboard')->with('error', 'Approved listings cannot be edited.');
        }

        $brands = Brand::active()->orderBy('name')->get();
        $fuelTypes = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid', 'cng' => 'CNG'];
        $transmissions = ['manual' => 'Manual', 'automatic' => 'Automatic'];

        return Inertia::render('Customer/Listings/Edit', [
            'listing' => [
                'id' => $listing->id,
                'unique_id' => $listing->unique_id,
                'title' => $listing->title,
                'brand_id' => $listing->brand_id,
                'model' => $listing->model,
                'year' => $listing->year,
                'fuel_type' => $listing->fuel_type,
                'transmission' => $listing->transmission,
                'km_driven' => $listing->km_driven,
                'price' => (float) ($listing->price ?? 0),
                'city' => $listing->city,
                'registration_number' => $listing->registration_number,
                'owners' => $listing->owners,
                'owner_email' => $listing->owner_email ?: $customer->email,
                'whatsapp_number' => $listing->whatsapp_number ?: $customer->whatsapp_number ?: $customer->phone,
                'latitude' => $listing->latitude,
                'longitude' => $listing->longitude,
                'images' => collect(json_decode($listing->images ?: '[]', true))
                    ->filter()
                    ->map(fn (string $path) => asset('storage/'.$path))
                    ->values(),
            ],
            'options' => [
                'brands' => $brands->map(fn (Brand $brand) => ['id' => $brand->id, 'name' => $brand->name]),
                'fuelTypes' => collect($fuelTypes)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->values(),
                'transmissions' => collect($transmissions)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->values(),
            ],
            'actions' => [
                'update' => route('customer.listing.update', $listing->id),
                'dashboard' => route('customer.dashboard'),
            ],
        ]);
    }

    public function updateListing(Request $request, $id)
    {
        $customer = Auth::guard('customer')->user();
        $listing = CustomerCarListing::where('id', $id)
            ->where('owner_phone', $customer->phone)
            ->firstOrFail();

        if ($listing->status === 'approved') {
            return redirect()->route('customer.dashboard')->with('error', 'Approved listings cannot be edited.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'brand_id' => 'nullable|exists:brands,id',
            'model' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1900|max:'.date('Y'),
            'fuel_type' => 'nullable|in:petrol,diesel,electric,hybrid,cng',
            'transmission' => 'nullable|in:manual,automatic',
            'km_driven' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'city' => 'nullable|string|max:100',
            'registration_number' => 'nullable|string|max:20',
            'owners' => 'nullable|integer|min:1|max:10',
            'owner_email' => 'nullable|email|max:255',
            'whatsapp_number' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'images' => 'nullable|array|min:5|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePaths = json_decode($listing->images ?? '[]', true);
        if ($request->hasFile('images')) {
            foreach ($imagePaths as $img) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($img);
            }
            $imagePaths = [];

            $files = $request->file('images');
            
            foreach ($files as $index => $image) {
                $filename = \Illuminate\Support\Str::uuid().'.'.$image->getClientOriginalExtension();
                $path = $image->storeAs('customer-listings', $filename, 'public');
                
                try {
                    $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($path);
                    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
                    $img = $manager->read($fullPath);
                    if ($img->width() > 800) {
                        $img->scaleDown(width: 800);
                        $img->save($fullPath, quality: 75);
                    }
                } catch (\Exception $e) {}

                $imagePaths[] = $path;
            }
        }

        $listing->update([
            'title' => $request->title,
            'brand_id' => $request->brand_id,
            'model' => $request->model,
            'year' => $request->year,
            'fuel_type' => $request->fuel_type,
            'transmission' => $request->transmission,
            'km_driven' => $request->km_driven,
            'price' => $request->price,
            'city' => $request->city,
            'registration_number' => $request->registration_number,
            'owners' => $request->owners ?? 1,
            'images' => json_encode($imagePaths),
            'owner_email' => $request->owner_email ?? $listing->owner_email,
            'whatsapp_number' => $request->whatsapp_number ?? $listing->whatsapp_number,
            'latitude' => $request->latitude ?? $listing->latitude,
            'longitude' => $request->longitude ?? $listing->longitude,
            'status' => 'pending', // Reset to pending if it was rejected
        ]);

        $email = $listing->owner_email ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('email');
        if ($email) {
            try {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\UserListingStatusNotification($listing, 'updated'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send update email: ' . $e->getMessage());
            }
        }

        return redirect()->route('customer.dashboard')->with('success', 'Listing updated successfully and is pending review.');
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('home');
    }

    private function mapCustomer(Customer $customer): array
    {
        return [
            'id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'customer_unique_id' => $customer->customer_unique_id,
            'profile_image_url' => $customer->profile_image ? asset('storage/'.$customer->profile_image) : null,
            'profile_completion_percentage' => (int) $customer->profile_completion_percentage,
        ];
    }

    private function mapListing(CustomerCarListing $listing): array
    {
        $images = collect(json_decode($listing->images ?: '[]', true))
            ->filter()
            ->map(fn (string $path) => asset('storage/'.$path))
            ->values();

        return [
            'id' => $listing->id,
            'unique_id' => $listing->unique_id,
            'title' => $listing->title,
            'brand' => $listing->brand?->name,
            'model' => $listing->model,
            'year' => $listing->year,
            'fuel_type' => $listing->fuel_type,
            'transmission' => $listing->transmission,
            'km_driven' => $listing->km_driven,
            'price' => (float) ($listing->price ?? 0),
            'city' => $listing->city,
            'registration_number' => $listing->registration_number,
            'status' => $listing->status,
            'rejection_reason' => $listing->rejection_reason,
            'image_url' => $images->first(),
            'is_featured' => $listing->isFeatured(),
            'featured_expires_at' => optional($listing->featured_expires_at)->format('d M Y'),
            'view_url' => $listing->status === 'approved' ? route('car.detail', $listing->slug) : null,
            'edit_url' => route('customer.listing.edit', $listing->id),
            'feature_url' => route('customer.listing.featured-plans', $listing),
            'delete_url' => route('customer.listing.delete', $listing->id),
        ];
    }
}
