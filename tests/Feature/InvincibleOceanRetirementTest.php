<?php

namespace Tests\Feature;

use App\Models\Dealer;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Invincible Ocean, which powered the e-challan, challan PDF and vehicle
 * service-history lookups, has shut down. The lookups are gone; what stays is
 * deliberate:
 *
 * - The public pages were indexed, so they redirect permanently to the RC
 *   lookup - the one paid lookup that still works - rather than 404ing.
 * - Every table and row is kept. They record payments customers made, and the
 *   admin history screens remain so a past payment can still be traced or
 *   refunded.
 * - Only the admin PRICING screens went, since there is nothing left to price.
 */
class InvincibleOceanRetirementTest extends TestCase
{
    use DatabaseTransactions;

    public static function retiredPublicUrls(): array
    {
        return [
            'challan search' => ['/challan-search'],
            'challan result' => ['/challan-search/9f1c2e44-0000-4000-8000-000000000000'],
            'service history' => ['/service-history'],
            'service history pdf' => ['/service-history/9f1c2e44-0000-4000-8000-000000000000/pdf'],
            'maruti' => ['/maruti-service-history'],
            'mahindra' => ['/mahindra-service-history/9f1c2e44-0000-4000-8000-000000000000'],
        ];
    }

    #[DataProvider('retiredPublicUrls')]
    public function test_a_retired_public_url_redirects_permanently_to_the_rc_lookup(string $url): void
    {
        $this->get($url)
            ->assertStatus(301)
            ->assertRedirect('/vehicle-search');
    }

    public function test_the_rc_lookup_still_works(): void
    {
        $this->get('/vehicle-search')->assertOk();
    }

    public static function adminHistoryScreens(): array
    {
        return [
            'dealer service history' => ['/admin/service-histories', 'Admin/ServiceHistories/Index'],
            'maruti dealers' => ['/admin/maruti-service-histories', 'Admin/ProviderServiceHistories/Index'],
            'maruti customers' => ['/admin/customer-maruti-service-histories', 'Admin/ProviderServiceHistories/Index'],
            'mahindra customers' => ['/admin/mahindra-service-histories', 'Admin/ProviderServiceHistories/Index'],
            'dealer challans' => ['/admin/challan-searches', 'Admin/ChallanSearches/Index'],
            'challan pdf logs' => ['/admin/challan-pdf/logs', 'Admin/ChallanPdf/Logs'],
            'combined service ledger' => ['/admin/service-tracking/service-history', 'Admin/ServiceTracking/ServiceHistory'],
            'combined challan ledger' => ['/admin/service-tracking/challan-search', 'Admin/ServiceTracking/ChallanSearch'],
        ];
    }

    #[DataProvider('adminHistoryScreens')]
    public function test_the_admin_history_screens_still_load(string $url, string $component): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get($url)
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component($component));
    }

    public static function retiredPricingScreens(): array
    {
        return [
            ['/admin/service-histories/settings'],
            ['/admin/maruti-service-histories/settings'],
            ['/admin/mahindra-service-histories/settings'],
            ['/admin/challan-searches/settings'],
        ];
    }

    #[DataProvider('retiredPricingScreens')]
    public function test_the_pricing_screens_are_gone(string $url): void
    {
        // These now fall through to the {record} show route and fail to bind,
        // which is the correct outcome for a page that no longer exists.
        $this->actingAs($this->admin(), 'admin')->get($url)->assertNotFound();
    }

    public function test_the_challan_pdf_landing_page_now_opens_its_logs(): void
    {
        // Its landing page was the pricing screen; the logs are what is left.
        $this->actingAs($this->admin(), 'admin')
            ->get('/admin/challan-pdf')
            ->assertRedirect('/admin/challan-pdf/logs');
    }

    public function test_a_dealer_bookmark_lands_on_the_rc_check(): void
    {
        $dealer = Dealer::query()->where('status', 'approved')->first();

        if (! $dealer) {
            $this->markTestSkipped('No approved dealer to sign in as.');
        }

        foreach (['/dealer/challan-search', '/dealer/challan-pdf', '/dealer/service-history', '/dealer/maruti-service-history'] as $url) {
            $this->actingAs($dealer, 'dealer')
                ->get($url)
                ->assertRedirect('/dealer/vehicle-search');
        }
    }

    private function admin(): User
    {
        $admin = User::first();

        if (! $admin) {
            $this->markTestSkipped('No admin account to sign in as.');
        }

        return $admin;
    }
}
