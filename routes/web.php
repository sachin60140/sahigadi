<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CarController as AdminCarController;
use App\Http\Controllers\Admin\CustomerCarListingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DealerController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\PaymentSettingsController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\ServiceHistoryController as AdminServiceHistoryController;
use App\Http\Controllers\Admin\ServiceTrackingController;
use App\Http\Controllers\Admin\VehicleSearchController as AdminVehicleSearchController;
use App\Http\Controllers\Dealer\AuthController;
use App\Http\Controllers\Dealer\CarController as DealerCarController;
use App\Http\Controllers\Dealer\DashboardController as DealerDashboardController;
use App\Http\Controllers\Dealer\EnquiryController;
use App\Http\Controllers\Dealer\ProfileController;
use App\Http\Controllers\Dealer\MarutiServiceHistoryController;
use App\Http\Controllers\Dealer\PlanController;
use App\Http\Controllers\Dealer\ServiceHistoryController;
use App\Http\Controllers\Dealer\VehicleSearchController;
use App\Http\Controllers\Dealer\WalletController;
use App\Http\Controllers\Frontend\CarController;
use App\Http\Controllers\Frontend\ChallanSearchController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\SellCarController;
use App\Http\Controllers\Frontend\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

// TEMPORARY MIGRATE ROUTE
Route::get('/run-live-migration', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('optimize:clear');
        return "Migration and cache clear completed successfully! <br><br> " . nl2br(Artisan::output());
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/cars', [CarController::class, 'index'])->name('cars.index');
Route::get('/car/{slug}', [CarController::class, 'show'])->name('car.detail');
Route::post('/car/{slug}/enquiry', [CarController::class, 'enquiry'])->name('car.enquiry');

// Contact Unlock OTP Routes
Route::post('/api/enquiry/send-otp', [\App\Http\Controllers\Frontend\EnquiryApiController::class, 'sendOtp'])->name('api.enquiry.send-otp');
Route::post('/api/enquiry/verify-otp', [\App\Http\Controllers\Frontend\EnquiryApiController::class, 'verifyOtp'])->name('api.enquiry.verify-otp');
Route::get('/used-cars-in-{city}', [CarController::class, 'byCity'])
    ->name('cars.city');
Route::get('/used-{brand}-cars', [CarController::class, 'byBrand'])
    ->name('cars.brand')
    ->where('brand', '[a-zA-Z0-9\-]+');
Route::get('/used-{brand}-cars-in-{city}', [CarController::class, 'byBrand'])
    ->name('cars.brand.city')
    ->where('brand', '[a-zA-Z0-9\-]+')
    ->where('city', '[a-zA-Z0-9\-]+');
Route::get('/catalog/{slug}', [CarController::class, 'dealerCatalog'])->name('dealer.catalog');
Route::get('/verified-dealers', [CarController::class, 'verifiedDealers'])->name('verified-dealers');

Route::get('/sell-your-car', [SellCarController::class, 'index'])->name('sell-car.index');
Route::post('/sell-your-car', [SellCarController::class, 'store'])->name('sell-car.store');
Route::post('/sell-your-car/send-otp', [SellCarController::class, 'sendOtp'])->name('sell-car.send-otp');
Route::post('/sell-your-car/verify-otp', [SellCarController::class, 'verifyOtp'])->name('sell-car.verify-otp');

Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/', function () {
        return auth('customer')->check() ? redirect()->route('customer.dashboard') : redirect()->route('customer.login');
    });

    Route::middleware('guest:customer')->group(function () {
        Route::get('/login', [CustomerController::class, 'showLogin'])->name('login');
        Route::post('/send-otp', [CustomerController::class, 'sendOtp'])->name('send-otp');
        Route::post('/verify-otp', [CustomerController::class, 'verifyOtp'])->name('verify-otp');
    });

    Route::middleware('auth:customer')->group(function () {
        Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
        Route::post('/send-delete-otp', [CustomerController::class, 'sendDeleteOtp'])->name('listing.delete.otp');
        Route::delete('/listing/{id}', [CustomerController::class, 'deleteListing'])->name('listing.delete');
        Route::get('/listing/{id}/edit', [CustomerController::class, 'editListing'])->name('listing.edit');
        Route::put('/listing/{id}', [CustomerController::class, 'updateListing'])->name('listing.update');
        Route::get('/profile', [CustomerController::class, 'editProfile'])->name('profile.edit');
        Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
        Route::get('/wallet', [\App\Http\Controllers\Frontend\CustomerWalletController::class, 'index'])->name('wallet.index');
        Route::get('/wallet/add', [\App\Http\Controllers\Frontend\CustomerWalletController::class, 'add'])->name('wallet.add');
        Route::get('/wallet/transaction/{id}/receipt', [\App\Http\Controllers\Frontend\CustomerWalletController::class, 'downloadReceipt'])->name('wallet.receipt');

        Route::get('/payments/checkout', [\App\Http\Controllers\Frontend\CustomerPaymentController::class, 'checkout'])->name('payments.checkout');
        Route::post('/payments/success', [\App\Http\Controllers\Frontend\CustomerPaymentController::class, 'success'])->name('payments.success');
        Route::get('/payments/failed', [\App\Http\Controllers\Frontend\CustomerPaymentController::class, 'failed'])->name('payments.failed');

        Route::post('/payments/phonepe/initiate', [\App\Http\Controllers\Frontend\CustomerPaymentController::class, 'phonepeInitiate'])->name('payments.phonepe.initiate');
        Route::match(['GET', 'POST'], '/payments/phonepe/callback', [\App\Http\Controllers\Frontend\CustomerPaymentController::class, 'phonepeCallback'])->name('payments.phonepe.callback');

        Route::post('/logout', [CustomerController::class, 'logout'])->name('logout');
    });
});

Route::prefix('dealer')->name('dealer.')->group(function () {
    Route::get('/', function () {
        return auth('dealer')->check() ? redirect()->route('dealer.dashboard') : redirect()->route('dealer.login');
    });

    Route::middleware('guest:dealer')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login']);
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/send-otp', [AuthController::class, 'sendOtp'])->name('send-otp');
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp');

        Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('/forgot-password/send-otp', [AuthController::class, 'sendResetOtp'])->name('forgot-password.send-otp');
        Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])->name('forgot-password.reset');
    });

    Route::middleware('auth:dealer')->group(function () {
        Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DealerDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/phone-otp', [ProfileController::class, 'sendPhoneOtp'])->name('profile.phone-otp');
        Route::post('/profile/verify-phone', [ProfileController::class, 'verifyPhoneOtp'])->name('profile.verify-phone');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

        Route::middleware('dealer.approval')->group(function () {
            Route::resource('cars', DealerCarController::class);
            Route::delete('/cars/{car}/image/{carImage}', [DealerCarController::class, 'deleteImage'])->name('cars.image.delete');
            Route::post('/cars/{car}/image/{carImage}/primary', [DealerCarController::class, 'setPrimaryImage'])->name('cars.image.primary');
            Route::post('/cars/{car}/featured', [DealerCarController::class, 'makeFeatured'])->name('cars.featured');

            Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
            Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('enquiries.show');
            Route::post('/enquiries/{enquiry}/contacted', [EnquiryController::class, 'markContacted'])->name('enquiries.contacted');

            Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
            Route::get('/wallet/add', [WalletController::class, 'add'])->name('wallet.add');
            Route::get('/wallet/transaction/{id}/receipt', [WalletController::class, 'downloadReceipt'])->name('wallet.receipt');

            Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
            Route::get('/plans/{plan}', [PlanController::class, 'show'])->name('plans.show');
            Route::post('/plans/{plan}/purchase', [PlanController::class, 'purchase'])->name('plans.purchase');

            Route::get('/vehicle-search', [VehicleSearchController::class, 'index'])->name('vehicle-search.index');
            Route::post('/vehicle-search/search', [VehicleSearchController::class, 'search'])->name('vehicle-search.search');
            Route::get('/vehicle-search/{vehicleSearch}', [VehicleSearchController::class, 'show'])->name('vehicle-search.show');
            Route::get('/vehicle-search/{vehicleSearch}/pdf', [VehicleSearchController::class, 'exportPdf'])->name('vehicle-search.pdf');

            Route::get('/service-history', [ServiceHistoryController::class, 'index'])->name('service-history.index');
            Route::post('/service-history/search', [ServiceHistoryController::class, 'search'])->name('service-history.search');
            Route::get('/service-history/{serviceHistory}', [ServiceHistoryController::class, 'show'])->name('service-history.show');
            Route::get('/service-history/{serviceHistory}/pdf', [ServiceHistoryController::class, 'downloadPdf'])->name('service-history.pdf');

            Route::get('/maruti-service-history', [MarutiServiceHistoryController::class, 'index'])->name('maruti-service-history.index');
            Route::post('/maruti-service-history/search', [MarutiServiceHistoryController::class, 'search'])->name('maruti-service-history.search');
            Route::get('/maruti-service-history/{marutiServiceHistory}', [MarutiServiceHistoryController::class, 'show'])->name('maruti-service-history.show');
            Route::get('/maruti-service-history/{marutiServiceHistory}/pdf', [MarutiServiceHistoryController::class, 'downloadPdf'])->name('maruti-service-history.pdf');

            Route::get('/challan-search', [App\Http\Controllers\Dealer\ChallanSearchController::class, 'index'])->name('challan-search.index');
            Route::post('/challan-search/search', [App\Http\Controllers\Dealer\ChallanSearchController::class, 'search'])->name('challan-search.search');
            Route::get('/challan-search/{challanSearch}', [App\Http\Controllers\Dealer\ChallanSearchController::class, 'show'])->name('challan-search.show');
            Route::get('/challan-search/{challanSearch}/pdf', [App\Http\Controllers\Dealer\ChallanSearchController::class, 'exportPdf'])->name('challan-search.pdf');
        });

        Route::get('/payments/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
        Route::post('/payments/success', [PaymentController::class, 'success'])->name('payments.success');
        Route::get('/payments/failed', [PaymentController::class, 'failed'])->name('payments.failed');

        Route::post('/payments/phonepe/initiate', [PaymentController::class, 'phonepeInitiate'])->name('payments.phonepe.initiate');
        Route::match(['GET', 'POST'], '/payments/phonepe/callback', [PaymentController::class, 'phonepeCallback'])->name('payments.phonepe.callback');
    });
});

// Webhook should be globally accessible (not under /dealer/)
Route::post('/payments/phonepe/webhook', [PaymentController::class, 'phonepeWebhook'])->name('payments.phonepe.webhook');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return auth('admin')->check() ? redirect()->route('admin.dashboard') : redirect()->route('admin.login');
    });

    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminDashboardController::class, 'login'])->name('login');
        Route::post('/authenticate', [AdminDashboardController::class, 'authenticate'])->name('authenticate');
    });

    Route::middleware('admin.access')->group(function () {
        Route::get('/logout', [AdminDashboardController::class, 'logout'])->name('logout');
        Route::get('/change-password', [AdminDashboardController::class, 'changePassword'])->name('change-password');
        Route::post('/change-password', [AdminDashboardController::class, 'changePassword']);
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('dealers', DealerController::class);
        Route::post('/dealers/{dealer}/approve', [DealerController::class, 'approve'])->name('dealers.approve');
        Route::post('/dealers/{dealer}/reject', [DealerController::class, 'reject'])->name('dealers.reject');
        Route::post('/dealers/{dealer}/toggle-status', [DealerController::class, 'toggleStatus'])->name('dealers.toggle-status');
        Route::post('/dealers/{dealer}/add-money', [DealerController::class, 'addMoney'])->name('dealers.add-money');
        Route::post('/dealers/{dealer}/debit-money', [DealerController::class, 'debitMoney'])->name('dealers.debit-money');
        Route::post('/dealers/{dealer}/assign-plan', [DealerController::class, 'assignPlan'])->name('dealers.assign-plan');
        Route::post('/dealers/{dealer}/verify-gst', [DealerController::class, 'verifyGst'])->name('dealers.verify-gst');
        Route::post('/dealers/{dealer}/unverify-gst', [DealerController::class, 'unverifyGst'])->name('dealers.unverify-gst');

        Route::resource('cars', AdminCarController::class);
        Route::post('/cars/{car}/approve', [AdminCarController::class, 'approve'])->name('cars.approve');
        Route::post('/cars/{car}/reject', [AdminCarController::class, 'reject'])->name('cars.reject');
        Route::post('/cars/{car}/featured', [AdminCarController::class, 'featured'])->name('cars.featured');
        Route::post('/cars/{car}/remove-featured', [AdminCarController::class, 'removeFeatured'])->name('cars.remove-featured');
        Route::delete('/cars/{car}/image/{carImage}', [AdminCarController::class, 'deleteImage'])->name('cars.image.delete');
        Route::post('/cars/{car}/image/{carImage}/primary', [AdminCarController::class, 'setPrimaryImage'])->name('cars.image.primary');

        Route::resource('plans', AdminPlanController::class);
        Route::resource('brands', BrandController::class);
        
        Route::resource('customers', App\Http\Controllers\Admin\CustomerController::class)->only(['index', 'show', 'edit', 'update']);

        Route::get('/enquiries/export/excel', [AdminEnquiryController::class, 'exportExcel'])->name('enquiries.exportExcel');
        Route::resource('enquiries', AdminEnquiryController::class)->only(['index', 'show']);
        Route::post('/enquiries/{enquiry}/contacted', [AdminEnquiryController::class, 'markContacted'])->name('enquiries.contacted');

        Route::resource('contact-enquiries', App\Http\Controllers\Admin\ContactEnquiryController::class)->only(['index', 'show', 'destroy']);
        Route::post('/contact-enquiries/{contact_enquiry}/read', [App\Http\Controllers\Admin\ContactEnquiryController::class, 'markAsRead'])->name('contact-enquiries.read');

        Route::get('/customer-listings/export/excel', [CustomerCarListingController::class, 'exportExcel'])->name('customer-listings.exportExcel');
        Route::get('/customer-listings/export/pdf', [CustomerCarListingController::class, 'exportPdf'])->name('customer-listings.exportPdf');
        Route::resource('customer-listings', CustomerCarListingController::class);
        Route::post('/customer-listings/{customer_listing}/approve', [CustomerCarListingController::class, 'approve'])->name('customer-listings.approve');
        Route::post('/customer-listings/{customer_listing}/reject', [CustomerCarListingController::class, 'reject'])->name('customer-listings.reject');
        Route::post('/customer-listings/{customer_listing}/featured', [CustomerCarListingController::class, 'makeFeatured'])->name('customer-listings.featured');
        Route::post('/customer-listings/{customer_listing}/remove-featured', [CustomerCarListingController::class, 'removeFeatured'])->name('customer-listings.remove-featured');
        Route::post('/customer-listings/{customer_listing}/delete-image', [CustomerCarListingController::class, 'deleteImage'])->name('customer-listings.image.delete');

        Route::get('/vehicle-searches', [AdminVehicleSearchController::class, 'index'])->name('vehicle-searches.index');
        Route::get('/vehicle-searches/settings', [AdminVehicleSearchController::class, 'settings'])->name('vehicle-searches.settings');
        Route::post('/vehicle-searches/settings', [AdminVehicleSearchController::class, 'settings']);
        Route::get('/vehicle-searches/{vehicleSearch}', [AdminVehicleSearchController::class, 'show'])->name('vehicle-searches.show');
        Route::get('/vehicle-searches/export/excel', [AdminVehicleSearchController::class, 'exportExcel'])->name('vehicle-searches.exportExcel');
        Route::get('/vehicle-searches/export/pdf', [AdminVehicleSearchController::class, 'exportPdf'])->name('vehicle-searches.exportPdf');
        Route::get('/vehicle-searches/{vehicleSearch}/pdf', [AdminVehicleSearchController::class, 'downloadSinglePdf'])->name('vehicle-searches.downloadPdf');

        Route::get('/customer-vehicle-searches', [App\Http\Controllers\Admin\CustomerVehicleSearchController::class, 'index'])->name('customer-vehicle-searches.index');
        Route::get('/customer-vehicle-searches/export/excel', [App\Http\Controllers\Admin\CustomerVehicleSearchController::class, 'exportExcel'])->name('customer-vehicle-searches.exportExcel');
        Route::get('/customer-vehicle-searches/export/pdf', [App\Http\Controllers\Admin\CustomerVehicleSearchController::class, 'exportPdf'])->name('customer-vehicle-searches.exportPdf');
        Route::get('/customer-vehicle-searches/{vehicleSearch}', [App\Http\Controllers\Admin\CustomerVehicleSearchController::class, 'show'])->name('customer-vehicle-searches.show');
        Route::get('/customer-vehicle-searches/{vehicleSearch}/pdf', [App\Http\Controllers\Admin\CustomerVehicleSearchController::class, 'downloadPdf'])->name('customer-vehicle-searches.downloadPdf');

        Route::get('/service-histories', [AdminServiceHistoryController::class, 'index'])->name('service-histories.index');
        Route::get('/service-histories/settings', [AdminServiceHistoryController::class, 'settings'])->name('service-histories.settings');
        Route::post('/service-histories/settings', [AdminServiceHistoryController::class, 'settings']);
        Route::get('/service-histories/{serviceHistory}', [AdminServiceHistoryController::class, 'show'])->name('service-histories.show');
        Route::get('/service-histories/export/excel', [AdminServiceHistoryController::class, 'exportExcel'])->name('service-histories.exportExcel');
        Route::get('/service-histories/export/pdf', [AdminServiceHistoryController::class, 'exportPdf'])->name('service-histories.exportPdf');
        Route::get('/service-histories/{serviceHistory}/pdf', [AdminServiceHistoryController::class, 'downloadSinglePdf'])->name('service-histories.downloadPdf');

        Route::get('/maruti-service-histories', [App\Http\Controllers\Admin\MarutiServiceHistoryController::class, 'index'])->name('maruti-service-histories.index');
        Route::get('/maruti-service-histories/settings', [App\Http\Controllers\Admin\MarutiServiceHistoryController::class, 'settings'])->name('maruti-service-histories.settings');
        Route::post('/maruti-service-histories/settings', [App\Http\Controllers\Admin\MarutiServiceHistoryController::class, 'settings']);
        Route::get('/maruti-service-histories/{marutiServiceHistory}', [App\Http\Controllers\Admin\MarutiServiceHistoryController::class, 'show'])->name('maruti-service-histories.show');
        Route::get('/maruti-service-histories/export/excel', [App\Http\Controllers\Admin\MarutiServiceHistoryController::class, 'exportExcel'])->name('maruti-service-histories.exportExcel');
        Route::get('/maruti-service-histories/export/pdf', [App\Http\Controllers\Admin\MarutiServiceHistoryController::class, 'exportPdf'])->name('maruti-service-histories.exportPdf');
        Route::get('/maruti-service-histories/{marutiServiceHistory}/pdf', [App\Http\Controllers\Admin\MarutiServiceHistoryController::class, 'downloadPdf'])->name('maruti-service-histories.downloadPdf');

        Route::get('/customer-maruti-service-histories', [App\Http\Controllers\Admin\CustomerMarutiServiceHistoryController::class, 'index'])->name('customer-maruti-service-histories.index');
        Route::get('/customer-maruti-service-histories/export/excel', [App\Http\Controllers\Admin\CustomerMarutiServiceHistoryController::class, 'exportExcel'])->name('customer-maruti-service-histories.exportExcel');
        Route::get('/customer-maruti-service-histories/export/pdf', [App\Http\Controllers\Admin\CustomerMarutiServiceHistoryController::class, 'exportPdf'])->name('customer-maruti-service-histories.exportPdf');
        Route::get('/customer-maruti-service-histories/{marutiServiceHistory}', [App\Http\Controllers\Admin\CustomerMarutiServiceHistoryController::class, 'show'])->name('customer-maruti-service-histories.show');
        Route::get('/customer-maruti-service-histories/{marutiServiceHistory}/pdf', [App\Http\Controllers\Admin\CustomerMarutiServiceHistoryController::class, 'downloadPdf'])->name('customer-maruti-service-histories.downloadPdf');

        Route::get('/mahindra-service-histories', [App\Http\Controllers\Admin\MahindraServiceHistoryController::class, 'index'])->name('mahindra-service-histories.index');
        Route::get('/mahindra-service-histories/settings', [App\Http\Controllers\Admin\MahindraServiceHistoryController::class, 'settings'])->name('mahindra-service-histories.settings');
        Route::post('/mahindra-service-histories/settings', [App\Http\Controllers\Admin\MahindraServiceHistoryController::class, 'settings']);
        Route::get('/mahindra-service-histories/export/excel', [App\Http\Controllers\Admin\MahindraServiceHistoryController::class, 'exportExcel'])->name('mahindra-service-histories.exportExcel');
        Route::get('/mahindra-service-histories/export/pdf', [App\Http\Controllers\Admin\MahindraServiceHistoryController::class, 'exportPdf'])->name('mahindra-service-histories.exportPdf');
        Route::get('/mahindra-service-histories/{mahindraServiceHistory}', [App\Http\Controllers\Admin\MahindraServiceHistoryController::class, 'show'])->name('mahindra-service-histories.show');
        Route::get('/mahindra-service-histories/{mahindraServiceHistory}/pdf', [App\Http\Controllers\Admin\MahindraServiceHistoryController::class, 'downloadPdf'])->name('mahindra-service-histories.downloadPdf');

        Route::get('/challan-searches', [App\Http\Controllers\Admin\ChallanSearchController::class, 'index'])->name('challan-searches.index');
        Route::get('/challan-searches/settings', [App\Http\Controllers\Admin\ChallanSearchController::class, 'settings'])->name('challan-searches.settings');
        Route::post('/challan-searches/settings', [App\Http\Controllers\Admin\ChallanSearchController::class, 'settings']);
        Route::get('/challan-searches/{challanSearch}', [App\Http\Controllers\Admin\ChallanSearchController::class, 'show'])->name('challan-searches.show');
        Route::get('/challan-searches/{challanSearch}/pdf', [App\Http\Controllers\Admin\ChallanSearchController::class, 'downloadPdf'])->name('challan-searches.download-pdf');
        Route::get('/challan-searches/export/excel', [App\Http\Controllers\Admin\ChallanSearchController::class, 'exportExcel'])->name('challan-searches.exportExcel');
        Route::get('/challan-searches/export/pdf', [App\Http\Controllers\Admin\ChallanSearchController::class, 'exportPdf'])->name('challan-searches.exportPdf');

        Route::get('/payment-settings', [PaymentSettingsController::class, 'index'])->name('payment-settings.index');
        Route::post('/payment-settings', [PaymentSettingsController::class, 'update'])->name('payment-settings.update');

        Route::get('/service-tracking/vehicle-search', [ServiceTrackingController::class, 'vehicleSearch'])->name('service-tracking.vehicle-search');
        Route::get('/service-tracking/service-history', [ServiceTrackingController::class, 'serviceHistory'])->name('service-tracking.service-history');
        
        Route::get('/service-tracking/challan-search', [ServiceTrackingController::class, 'challanSearch'])->name('service-tracking.challan-search');
        Route::get('/service-tracking/challan-search/{id}', [ServiceTrackingController::class, 'showChallan'])->name('service-tracking.challan-search.show');
        Route::get('/service-tracking/challan-search/{id}/pdf', [ServiceTrackingController::class, 'downloadChallanPdf'])->name('service-tracking.challan-search.pdf');
        
        Route::get('/wallet-recharges', [App\Http\Controllers\Admin\WalletRechargeController::class, 'index'])->name('wallet-recharges.index');
        Route::get('/wallet-recharges/export/excel', [App\Http\Controllers\Admin\WalletRechargeController::class, 'exportExcel'])->name('wallet-recharges.exportExcel');
        Route::get('/wallet-recharges/export/pdf', [App\Http\Controllers\Admin\WalletRechargeController::class, 'exportPdf'])->name('wallet-recharges.exportPdf');
        Route::get('/wallet-recharges/{id}/receipt', [App\Http\Controllers\Admin\WalletRechargeController::class, 'downloadReceipt'])->name('wallet-recharges.receipt');

        Route::get('/customer-wallet-recharges', [App\Http\Controllers\Admin\CustomerWalletRechargeController::class, 'index'])->name('customer-wallet-recharges.index');
        Route::get('/customer-wallet-recharges/export/excel', [App\Http\Controllers\Admin\CustomerWalletRechargeController::class, 'exportExcel'])->name('customer-wallet-recharges.exportExcel');
        Route::get('/customer-wallet-recharges/export/pdf', [App\Http\Controllers\Admin\CustomerWalletRechargeController::class, 'exportPdf'])->name('customer-wallet-recharges.exportPdf');
        Route::get('/customer-wallet-recharges/{id}/receipt', [App\Http\Controllers\Admin\CustomerWalletRechargeController::class, 'downloadReceipt'])->name('customer-wallet-recharges.receipt');
        Route::post('/customer-wallet-recharges/deduct', [App\Http\Controllers\Admin\CustomerWalletRechargeController::class, 'deductMoney'])->name('customer-wallet-recharges.deduct');

        Route::get('/customer-transactions', [App\Http\Controllers\Admin\CustomerTransactionController::class, 'index'])->name('customer-transactions.index');
        Route::get('/customer-transactions/{id}', [App\Http\Controllers\Admin\CustomerTransactionController::class, 'show'])->name('customer-transactions.show');
        Route::post('/customer-transactions/{id}/refund', [App\Http\Controllers\Admin\CustomerTransactionController::class, 'refund'])->name('customer-transactions.refund');
        
        Route::get('/payment-links', [App\Http\Controllers\Admin\PaymentLinkController::class, 'index'])->name('payment-links.index');
        Route::post('/payment-links', [App\Http\Controllers\Admin\PaymentLinkController::class, 'store'])->name('payment-links.store');
        Route::delete('/payment-links/{payment_link}', [App\Http\Controllers\Admin\PaymentLinkController::class, 'destroy'])->name('payment-links.destroy');
        Route::post('/payment-links/{paymentLink}/refresh', [App\Http\Controllers\Admin\PaymentLinkController::class, 'refresh'])->name('payment-links.refresh');
    });
});

Route::get('/pay-link/{payment_link}', [\App\Http\Controllers\Frontend\PaymentLinkController::class, 'show'])->name('pay.link');
Route::post('/pay-link/{payment_link}/checkout', [\App\Http\Controllers\Frontend\PaymentLinkController::class, 'checkout'])->name('pay.link.checkout');
Route::match(['GET', 'POST'], '/pay-link/phonepe/callback', [\App\Http\Controllers\Frontend\PaymentLinkController::class, 'phonepeCallback'])->name('pay.link.phonepe.callback');

Route::get('/page/{page}', function ($page) {
    return view('frontend.pages.'.$page);
})->name('page');

Route::get('/contact', function () {
    return view('frontend.pages.contact');
})->name('contact');

Route::post('/contact', [\App\Http\Controllers\Frontend\ContactController::class, 'store']);

Route::get('/privacy-policy', function () {
    return view('frontend.pages.privacy-policy');
})->name('privacy-policy');

Route::get('/terms-of-use', function () {
    return view('frontend.pages.terms-of-use');
})->name('terms-of-use');

Route::get('/refund-policy', function () {
    return view('frontend.pages.refund-policy');
})->name('refund-policy');

Route::get('/service-history', [App\Http\Controllers\Frontend\ServiceHistoryController::class, 'index'])->name('service-history.index');
Route::post('/service-history/search', [App\Http\Controllers\Frontend\ServiceHistoryController::class, 'search'])->name('service-history.search');
Route::get('/service-history/callback', [App\Http\Controllers\Frontend\ServiceHistoryController::class, 'paymentCallback'])->name('service-history.callback');
Route::post('/service-history/callback', [App\Http\Controllers\Frontend\ServiceHistoryController::class, 'paymentCallback']);

Route::get('/vehicle-search', [App\Http\Controllers\Frontend\VehicleSearchController::class, 'index'])->name('vehicle-search.index');
Route::post('/vehicle-search/search', [App\Http\Controllers\Frontend\VehicleSearchController::class, 'search'])->name('vehicle-search.search');
Route::get('/vehicle-search/callback', [App\Http\Controllers\Frontend\VehicleSearchController::class, 'paymentCallback'])->name('vehicle-search.callback');
Route::post('/vehicle-search/callback', [App\Http\Controllers\Frontend\VehicleSearchController::class, 'paymentCallback']);
Route::get('/vehicle-search/{vehicleSearch}', [App\Http\Controllers\Frontend\VehicleSearchController::class, 'show'])->name('vehicle-search.show');
Route::get('/vehicle-search/{vehicleSearch}/pdf', [App\Http\Controllers\Frontend\VehicleSearchController::class, 'downloadPdf'])->name('vehicle-search.pdf');

Route::get('/challan-search', [ChallanSearchController::class, 'index'])->name('challan-search.index');
Route::post('/challan-search/search', [ChallanSearchController::class, 'search'])->name('challan-search.search');
Route::get('/challan-search/callback', [ChallanSearchController::class, 'paymentCallback'])->name('challan-search.callback');
Route::post('/challan-search/callback', [ChallanSearchController::class, 'paymentCallback']);
Route::get('/service-history/{serviceHistory}', [App\Http\Controllers\Frontend\ServiceHistoryController::class, 'show'])->name('service-history.show');
Route::get('/service-history/{serviceHistory}/pdf', [App\Http\Controllers\Frontend\ServiceHistoryController::class, 'downloadPdf'])->name('service-history.download-pdf');

Route::get('/maruti-service-history', [App\Http\Controllers\Frontend\MarutiServiceHistoryController::class, 'index'])->name('maruti-service-history.index');
Route::post('/maruti-service-history/search', [App\Http\Controllers\Frontend\MarutiServiceHistoryController::class, 'search'])->name('maruti-service-history.search');
Route::get('/maruti-service-history/callback', [App\Http\Controllers\Frontend\MarutiServiceHistoryController::class, 'paymentCallback'])->name('maruti-service-history.callback');
Route::post('/maruti-service-history/callback', [App\Http\Controllers\Frontend\MarutiServiceHistoryController::class, 'paymentCallback']);
Route::get('/maruti-service-history/{marutiServiceHistory}', [App\Http\Controllers\Frontend\MarutiServiceHistoryController::class, 'show'])->name('maruti-service-history.show');
Route::get('/maruti-service-history/{marutiServiceHistory}/pdf', [App\Http\Controllers\Frontend\MarutiServiceHistoryController::class, 'downloadPdf'])->name('maruti-service-history.pdf');

Route::get('/mahindra-service-history', [App\Http\Controllers\Frontend\MahindraServiceHistoryController::class, 'index'])->name('mahindra-service-history.index');
Route::post('/mahindra-service-history/search', [App\Http\Controllers\Frontend\MahindraServiceHistoryController::class, 'search'])->name('mahindra-service-history.search');
Route::get('/mahindra-service-history/callback', [App\Http\Controllers\Frontend\MahindraServiceHistoryController::class, 'paymentCallback'])->name('mahindra-service-history.callback');
Route::post('/mahindra-service-history/callback', [App\Http\Controllers\Frontend\MahindraServiceHistoryController::class, 'paymentCallback']);
Route::get('/mahindra-service-history/{mahindraServiceHistory}', [App\Http\Controllers\Frontend\MahindraServiceHistoryController::class, 'show'])->name('mahindra-service-history.show');
Route::get('/mahindra-service-history/{mahindraServiceHistory}/pdf', [App\Http\Controllers\Frontend\MahindraServiceHistoryController::class, 'downloadPdf'])->name('mahindra-service-history.pdf');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Temporary route to run migrations on Hostinger Shared Hosting
Route::get('/run-migrations', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    return "Migrations executed successfully! Result: " . \Illuminate\Support\Facades\Artisan::output();
});

// Temporary route to optimize existing images
Route::get('/optimize-images', function () {
    $directories = ['customer-listings', 'dealer-listings', 'cars', 'customer-cars'];
    $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
    
    $count = 0;
    $output = "";
    foreach ($directories as $dir) {
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($dir)) {
            $files = \Illuminate\Support\Facades\Storage::disk('public')->files($dir);
            foreach ($files as $file) {
                if (preg_match('/\.(jpg|jpeg|png)$/i', $file)) {
                    $fullPath = \Illuminate\Support\Facades\Storage::disk('public')->path($file);
                    $size = filesize($fullPath);
                    
                    if ($size > 250000) {
                        try {
                            $image = $manager->read($fullPath);
                            $width = $image->width();
                            
                            if ($width > 800) {
                                $image->scaleDown(width: 800);
                                $image->save($fullPath, quality: 75);
                                $output .= "Optimized: {$file} <br>";
                                $count++;
                            }
                        } catch (\Exception $e) {
                            $output .= "Failed to optimize {$file}: " . $e->getMessage() . "<br>";
                        }
                    }
                }
            }
        }
    }
    
    return "Successfully optimized {$count} images! <br><br>" . $output;
});

// Dynamic OpenGraph Image Resizer (WhatsApp / Facebook compatibility)
Route::get('/og-image', function (\Illuminate\Http\Request $request) {
    $path = $request->get('path');
    if (!$path) return redirect(asset('images/og-image.png'));

    // Extract path if it's a full URL
    if (filter_var($path, FILTER_VALIDATE_URL)) {
        $parsedUrl = parse_url($path);
        $path = ltrim($parsedUrl['path'] ?? '', '/');
        // Remove 'storage/' prefix if present
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
    }

    $storagePath = storage_path('app/public/' . $path);
    if (!file_exists($storagePath)) {
        return redirect(asset('images/og-image.png'));
    }

    $cacheDir = storage_path('app/public/og-cache');
    if (!file_exists($cacheDir)) {
        mkdir($cacheDir, 0755, true);
    }

    // Cache based on filename and modification time to ensure updates
    $filemtime = filemtime($storagePath);
    $filename = md5($path . $filemtime) . '.jpg';
    $cachePath = $cacheDir . '/' . $filename;

    if (!file_exists($cachePath)) {
        try {
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $image = $manager->read($storagePath);
            // Crop to perfect WhatsApp/Facebook 1200x630 landscape size
            $image->cover(1200, 630);
            $image->toJpeg(80)->save($cachePath);
        } catch (\Exception $e) {
            return response()->file($storagePath);
        }
    }

    return response()->file($cachePath, [
        'Content-Type' => 'image/jpeg',
        'Cache-Control' => 'public, max-age=604800'
    ]);
})->name('og.image.generate');
