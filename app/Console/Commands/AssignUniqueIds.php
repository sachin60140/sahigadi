<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Dealer;

class AssignUniqueIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-unique-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign auto-generated unique IDs to existing customers and dealers who do not have one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to assign Unique IDs...');

        // Process Customers
        $customers = Customer::whereNull('customer_unique_id')->get();
        $this->info("Found {$customers->count()} customers without a Unique ID.");
        
        $cCount = 0;
        foreach ($customers as $customer) {
            $customer->customer_unique_id = 'CUS' . (10000 + $customer->id);
            $customer->saveQuietly();
            $cCount++;
        }

        // Process Dealers
        $dealers = Dealer::whereNull('dealer_unique_id')->get();
        $this->info("Found {$dealers->count()} dealers without a Unique ID.");

        $dCount = 0;
        foreach ($dealers as $dealer) {
            $dealer->dealer_unique_id = 'DLR' . (10000 + $dealer->id);
            $dealer->saveQuietly();
            $dCount++;
        }

        $this->info("Success! Assigned Unique IDs to {$cCount} Customers and {$dCount} Dealers.");
    }
}
