<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

class BladeArtifactContractTest extends TestCase
{
    public function test_all_non_browser_blade_artifacts_exist(): void
    {
        $views = [
            'inertia',
            'sitemap.index',
            'emails.admin.new_listing',
            'emails.customer_otp',
            'emails.customer_profile_updated',
            'emails.customer_self_profile_updated',
            'emails.dealer.new_enquiry',
            'emails.featured.expiry_reminder',
            'emails.featured.subscribed',
            'emails.user.new_listing',
            'emails.user_listing_status',
            'admin.challan-searches.pdf',
            'admin.customer-listings.pdf',
            'admin.customer-maruti-service-histories.pdf',
            'admin.customer-maruti-service-histories.single-pdf',
            'admin.customer-vehicle-searches.pdf',
            'admin.customer-vehicle-searches.single-pdf',
            'admin.customer-wallet-recharges.pdf',
            'admin.mahindra-service-histories.pdf',
            'admin.mahindra-service-histories.single-pdf',
            'admin.maruti-service-histories.pdf',
            'admin.maruti-service-histories.single-pdf',
            'admin.service-histories.pdf',
            'admin.service-histories.single-pdf',
            'admin.vehicle-searches.pdf',
            'admin.vehicle-searches.single-pdf',
            'admin.wallet-recharges.pdf',
            'dealer.challan-searches.pdf',
            'dealer.maruti-service-history.pdf',
            'dealer.service-history.pdf',
            'dealer.vehicle-search.pdf',
            'dealer.wallet.receipt-pdf',
            'frontend.customer.wallet.receipt-pdf',
            'frontend.mahindra-service-history.pdf',
            'frontend.maruti-service-history.pdf',
            'frontend.service-history.pdf',
            'frontend.vehicle-search.pdf',
        ];

        foreach ($views as $view) {
            $this->assertTrue(View::exists($view), "Expected Blade artifact [{$view}] to exist.");
        }
    }
}
