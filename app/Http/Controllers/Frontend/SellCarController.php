<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\CustomerCarListing;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SellCarController extends Controller
{
    public function index()
    {
        $brands = Brand::active()->orderBy('name')->get();
        $fuelTypes = ['petrol' => 'Petrol', 'diesel' => 'Diesel', 'electric' => 'Electric', 'hybrid' => 'Hybrid', 'cng' => 'CNG'];
        $transmissions = ['manual' => 'Manual', 'automatic' => 'Automatic'];

        return \Inertia\Inertia::render('Public/SellCar', compact('brands', 'fuelTypes', 'transmissions'));
    }

    public function store(Request $request)
    {
        if (auth('customer')->check()) {
            $request->merge([
                'owner_phone' => auth('customer')->user()->phone,
                'owner_name' => $request->owner_name ?? auth('customer')->user()->name,
                'owner_email' => $request->owner_email ?? auth('customer')->user()->email,
            ]);
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
            'owner_name' => 'nullable|string|max:255',
            'owner_email' => 'nullable|email|max:255',
            'owner_phone' => 'required|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'latitude.required' => 'Location access is strictly required to list a vehicle. Please allow location permissions in your browser.',
            'longitude.required' => 'Location data is missing. Please ensure location services are enabled.',
        ]);

        if (!auth('customer')->check() && session('sell_car_phone_verified') !== $request->owner_phone) {
            return redirect()->back()->withInput()->with('error', 'Please verify your phone number via OTP before submitting the listing.');
        }

        if ($request->hasFile('images')) {
            if (count($request->file('images')) < 5) {
                return redirect()->back()->withInput()->with('error', 'Please upload at least 5 images.');
            }
            if (count($request->file('images')) > 10) {
                return redirect()->back()->withInput()->with('error', 'Maximum 10 images allowed.');
            }
        }

        $slug = Str::slug($request->title).'-'.Str::random(5);
        while (CustomerCarListing::where('slug', $slug)->exists()) {
            $slug = Str::slug($request->title).'-'.Str::random(5);
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            $files = $request->file('images');
            $primaryIndex = (int) $request->input('primary_image_index', 0);
            
            foreach ($files as $index => $image) {
                $filename = Str::uuid().'.'.$image->getClientOriginalExtension();
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

            if ($primaryIndex > 0 && isset($imagePaths[$primaryIndex])) {
                $primaryImg = $imagePaths[$primaryIndex];
                unset($imagePaths[$primaryIndex]);
                array_unshift($imagePaths, $primaryImg);
                $imagePaths = array_values($imagePaths);
            }
        }

        $customerListing = CustomerCarListing::create([
            'title' => $request->title,
            'slug' => $slug,
            'brand_id' => $request->brand_id,
            'model' => $request->model,
            'year' => $request->year,
            'fuel_type' => $request->fuel_type,
            'transmission' => $request->transmission,
            'km_driven' => $request->km_driven,
            'price' => $request->price,
            'city' => $request->city,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'registration_number' => $request->registration_number,
            'owners' => $request->owners ?? 1,
            'owner_name' => $request->owner_name,
            'owner_phone' => $request->owner_phone,
            'whatsapp_number' => $request->whatsapp_number ?? $request->owner_phone,
            'owner_email' => $request->owner_email ?? null,
            'images' => json_encode($imagePaths),
            'status' => 'pending',
        ]);

        session()->forget('sell_car_phone_verified');

        try {
            \Illuminate\Support\Facades\Mail::to('sachin60140@gmail.com')->send(new \App\Mail\AdminNewListingNotification($customerListing, false));
            $email = $customerListing->owner_email ?: \App\Models\Customer::where('phone', $customerListing->owner_phone)->value('email');
            if ($email) {
                \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\UserNewListingNotification($customerListing));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send customer car listing emails: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Your car listing has been submitted successfully! We will review it and get back to you soon.');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^[0-9]{10}$/',
        ]);

        $phone = $request->phone;
        $otp = rand(100000, 999999);
        
        session(['sell_car_otp_' . $phone => $otp]);
        session(['sell_car_otp_time_' . $phone => now()]);

        $apiUrl = "https://pgapi.sparc.smartping.io/fe/api/v1/send";
        $text = "Hi! Your verification code is {$otp}. It is valid for 10 minutes. Please keep it confidential. - Sars Infotech Pvt Ltd";
        
        $response = \Illuminate\Support\Facades\Http::get($apiUrl, [
            'username' => 'sarsinfo.trans',
            'password' => '6E5s8aI_',
            'unicode' => 'false',
            'from' => 'INSARS',
            'text' => $text,
            'to' => $phone,
            'dltContentId' => '1707177677498830200',
            'dltPrincipalEntityId' => '1701166126846262605'
        ]);

        $customer = \App\Models\Customer::where('phone', $phone)->first();
        if ($customer && $customer->email) {
            try {
                \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\CustomerOtpMail($otp, 'phone verification'));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to send sell car OTP email: ' . $e->getMessage());
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

        $sessionOtp = session('sell_car_otp_' . $phone);
        $sessionTime = session('sell_car_otp_time_' . $phone);

        if (!$sessionOtp || !$sessionTime) {
            return response()->json(['success' => false, 'message' => 'OTP expired or not sent']);
        }

        if (now()->diffInMinutes($sessionTime) > 10) {
            return response()->json(['success' => false, 'message' => 'OTP has expired']);
        }

        if ((string)$sessionOtp === (string)$otp) {
            session(['sell_car_phone_verified' => $phone]);
            session()->forget('sell_car_otp_' . $phone);
            session()->forget('sell_car_otp_time_' . $phone);
            
            return response()->json(['success' => true, 'message' => 'Phone number verified successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Invalid OTP']);
    }
}
