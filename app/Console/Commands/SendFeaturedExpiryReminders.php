<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendFeaturedExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-featured-expiry-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders for featured plans expiring within 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDateStart = now();
        $targetDateEnd = now()->addHours(24);

        // Deal with Dealer Cars
        $dealerCars = \App\Models\Car::where('is_featured', true)
            ->whereBetween('featured_expires_at', [$targetDateStart, $targetDateEnd])
            ->get();

        foreach ($dealerCars as $car) {
            $this->sendReminder($car, $car->dealer->name, $car->dealer->email, route('dealer.cars.index'));
        }

        // Deal with Customer Cars
        $customerCars = \App\Models\CustomerCarListing::where('is_featured', true)
            ->whereBetween('featured_expires_at', [$targetDateStart, $targetDateEnd])
            ->get();

        foreach ($customerCars as $listing) {
            $email = $listing->owner_email ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('email');
            $customerName = $listing->owner_name ?: \App\Models\Customer::where('phone', $listing->owner_phone)->value('name');
            $this->sendReminder($listing, $customerName ?? 'Customer', $email, route('customer.dashboard'));
        }

        $this->info('Expiry reminders sent successfully.');
    }

    private function sendReminder($car, $userName, $userEmail, $actionUrl)
    {
        $emailDetails = [
            'car_id' => $car->unique_id,
            'car_title' => $car->title,
            'expires_at' => $car->featured_expires_at,
            'action_url' => $actionUrl
        ];

        try {
            if ($userEmail) {
                $emailDetails['is_admin'] = false;
                $emailDetails['user_name'] = $userName;
                \Illuminate\Support\Facades\Mail::to($userEmail)->send(new \App\Mail\FeaturedPlanExpiryReminder($emailDetails));
            }

            $emailDetails['is_admin'] = true;
            $emailDetails['user_name'] = 'Admin';
            \Illuminate\Support\Facades\Mail::to('sachin60140@gmail.com')->send(new \App\Mail\FeaturedPlanExpiryReminder($emailDetails));
        } catch (\Exception $e) {
            \Log::error('Failed to send expiry reminder email: ' . $e->getMessage());
        }
    }
}
