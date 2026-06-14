<?php

namespace Tests\Feature;

use App\Http\Controllers\Customer\FeaturedCarController as CustomerFeaturedCarController;
use App\Http\Controllers\Dealer\CarController;
use App\Http\Controllers\Dealer\EnquiryController;
use App\Models\Car;
use App\Models\Customer;
use App\Models\CustomerCarListing;
use App\Models\Dealer;
use App\Models\Enquiry;
use App\Services\SubscriptionService;
use Mockery;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AuthorizationBoundaryTest extends TestCase
{
    public function test_guests_are_redirected_from_admin_pages(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('admin.login'));
    }

    public function test_guests_are_redirected_from_dealer_pages(): void
    {
        $this->get(route('dealer.dashboard'))
            ->assertRedirect(route('dealer.login'));
    }

    public function test_guests_are_redirected_from_customer_pages(): void
    {
        $this->get(route('customer.dashboard'))
            ->assertRedirect(route('customer.login'));
    }

    public function test_dealers_cannot_edit_another_dealers_car(): void
    {
        $dealer = new Dealer();
        $dealer->id = 10;
        auth('dealer')->setUser($dealer);

        $car = new Car();
        $car->dealer_id = 11;

        $controller = new CarController(Mockery::mock(SubscriptionService::class));

        $this->assertForbidden(fn () => $controller->edit($car));
    }

    public function test_dealers_cannot_open_another_dealers_enquiry(): void
    {
        $dealer = new Dealer();
        $dealer->id = 10;
        auth('dealer')->setUser($dealer);

        $enquiry = new Enquiry();
        $enquiry->dealer_id = 11;

        $this->assertForbidden(fn () => (new EnquiryController())->show($enquiry));
    }

    public function test_customers_cannot_feature_another_customers_listing(): void
    {
        $customer = new Customer(['phone' => '9999999999']);
        $customer->id = 20;
        auth('customer')->setUser($customer);

        $listing = new CustomerCarListing(['owner_phone' => '8888888888']);

        $this->assertForbidden(fn () => (new CustomerFeaturedCarController())->showPlans($listing));
    }

    private function assertForbidden(callable $callback): void
    {
        try {
            $callback();
            $this->fail('Expected the request to be forbidden.');
        } catch (HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
    }
}
