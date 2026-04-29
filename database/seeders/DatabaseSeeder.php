<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Dealer;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@SAHIGADI.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Plan::create([
            'name' => 'Basic',
            'price' => 999,
            'listing_limit' => 5,
            'duration_days' => 30,
            'description' => 'Perfect for starting out. List up to 5 cars for 30 days.',
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Standard',
            'price' => 2499,
            'listing_limit' => 20,
            'duration_days' => 90,
            'description' => 'Great value. List up to 20 cars for 90 days.',
            'is_active' => true,
        ]);

        Plan::create([
            'name' => 'Premium',
            'price' => 4999,
            'listing_limit' => 50,
            'duration_days' => 180,
            'description' => 'For serious dealers. List up to 50 cars for 180 days.',
            'is_active' => true,
        ]);

        $brands = [
            'Maruti Suzuki',
            'Hyundai',
            'Honda',
            'Tata',
            'Mahindra',
            'Toyota',
            'Ford',
            'Volkswagen',
            'Skoda',
            'Kia',
            'Nissan',
            'Renault',
            'MG',
            'Jeep',
            'BMW',
            'Mercedes-Benz',
            'Audi',
            'Land Rover',
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand,
                'slug' => Str::slug($brand),
                'is_active' => true,
            ]);
        }

        $dealer = Dealer::create([
            'name' => 'Demo Dealer',
            'email' => 'dealer@example.com',
            'phone' => '9876543210',
            'password' => Hash::make('password'),
            'company_name' => 'Demo Cars',
            'city' => 'Mumbai',
            'status' => 'approved',
        ]);

        $dealer->wallet()->create(['balance' => 5000]);
    }
}
