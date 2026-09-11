<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Consumer\AddressController;
use App\Http\Controllers\Consumer\DashboardController as ConsumerDashboardController;
use App\Http\Controllers\Consumer\OrderController as ConsumerOrderController;
use App\Http\Controllers\Consumer\ProfileController as ConsumerProfileController;
use App\Http\Controllers\Consumer\NotificationController;
use App\Http\Controllers\Consumer\FavoriteController;
use App\Http\Controllers\Consumer\ReviewController;
use App\Http\Controllers\Retailer\DashboardController as RetailerDashboardController;
use App\Http\Controllers\Retailer\OrderController as RetailerOrderController;
use App\Http\Controllers\Retailer\InventoryController as RetailerInventoryController;
use App\Http\Controllers\Retailer\ProcurementController as RetailerProcurementController;
use App\Http\Controllers\Retailer\SettingsController as RetailerSettingsController;
use App\Http\Controllers\Wholesaler\DashboardController as WholesalerDashboardController;
use App\Http\Controllers\Wholesaler\OrderController as WholesalerOrderController;
use App\Http\Controllers\Wholesaler\ProductController as WholesalerProductController;
use App\Http\Controllers\Wholesaler\SettingsController as WholesalerSettingsController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\PricingController as AdminPricingController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\LogController as AdminLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InterfacePreferenceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. UKURASA WA KWANZA (LANDING PAGE)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/webhooks/clickpesa', [PaymentWebhookController::class, 'clickPesa'])->name('webhooks.clickpesa');

// 2. AUTHENTICATION ROUTES (Laravel Breeze)
require __DIR__.'/auth.php';

// Interface preferences are also available before account verification.
Route::post('/interface-preferences', [InterfacePreferenceController::class, 'update'])
    ->middleware('auth')
    ->name('interface-preferences.update');

// 3. ROUTES ZINAZOHITAJI MTUMIAJI KUWA AMEINGIA (AUTHENTICATED)
Route::middleware(['auth', 'verified'])->group(function () {

    // =====================================================
    // DASHBOARD - Inaelekeza kulingana na jukumu la mtumiaji
    // =====================================================
    Route::get('/dashboard', function () {
        $user = auth()->user();
        return match ($user->user_type) {
            'admin'      => redirect()->route('admin.dashboard'),
            'retailer'   => redirect()->route('retailer.dashboard'),
            'wholesaler' => redirect()->route('wholesaler.dashboard'),
            default      => redirect()->route('consumer.dashboard'),
        };
    })->name('dashboard');

    // =====================================================
    // PROFILE ROUTES
    // =====================================================
    Route::get('/profile', function () {
        $user = auth()->user();
        return match ($user->user_type) {
            'consumer'  => redirect()->route('consumer.profile'),
            'retailer'  => redirect()->route('retailer.settings.shop'),
            'wholesaler' => redirect()->route('wholesaler.settings.profile'),
            'admin'     => redirect()->route('admin.settings.profile'),
            default     => redirect()->route('consumer.profile'),
        };
    })->name('profile');

    Route::get('/profile/edit', function () {
        $user = auth()->user();
        return match ($user->user_type) {
            'consumer'  => redirect()->route('consumer.profile.edit'),
            'retailer'  => redirect()->route('retailer.settings.shop'),
            'wholesaler' => redirect()->route('wholesaler.settings.profile'),
            'admin'     => redirect()->route('admin.settings.profile'),
            default     => redirect()->route('consumer.profile.edit'),
        };
    })->name('profile.edit');

    // =====================================================
    // CHAT ROUTES (Global)
    // =====================================================
    Route::get('/chat/{orderId}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/send/{orderId}', [ChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages/{orderId}', [ChatController::class, 'fetch'])->name('chat.fetch');

    /*
    |--------------------------------------------------------------------------
    | CONSUMER ROUTES (Mtumiaji wa Mwisho)
    |--------------------------------------------------------------------------
    */
    Route::prefix('consumer')->name('consumer.')->group(function () {
        
        Route::get('/dashboard', [ConsumerDashboardController::class, 'index'])->name('dashboard');
        
        // Order Creation
        Route::get('/order/create/{type?}', [ConsumerOrderController::class, 'create'])->name('order.create');
        Route::post('/order/find-retailers', [ConsumerOrderController::class, 'findRetailers'])->name('order.find-retailers');
        Route::get('/order/payment-methods/{retailerId}', [ConsumerOrderController::class, 'paymentMethods'])->name('order.payment-methods');
        Route::get('/order/select-retailer', [ConsumerOrderController::class, 'selectRetailer'])->name('order.select-retailer');
        Route::post('/order/place-order', [ConsumerOrderController::class, 'placeOrder'])->name('order.place-order');
        Route::post('/order/store', [ConsumerOrderController::class, 'store'])->name('order.store');
        Route::post('/order/{order}/pay', [PaymentController::class, 'payRetail'])->name('order.pay');
        Route::post('/order/{order}/payment-submitted', [PaymentController::class, 'submitRetailManualPayment'])->name('order.payment-submitted');
        
        // Order Tracking
        Route::get('/order/tracking/{id?}', [ConsumerOrderController::class, 'tracking'])->name('order.tracking');
        Route::get('/order/details/{id}', [ConsumerOrderController::class, 'details'])->name('order.details');
        Route::post('/order/cancel/{id}', [ConsumerOrderController::class, 'cancel'])->name('order.cancel');
        Route::post('/order/reorder/{id}', [ConsumerOrderController::class, 'reorder'])->name('order.reorder');
        Route::get('/order/receipt/{id}', [ConsumerOrderController::class, 'receipt'])->name('order.receipt');
        
        // Reviews
        Route::post('/order/{id}/review', [ReviewController::class, 'store'])->name('order.review');
        Route::put('/review/{id}', [ReviewController::class, 'update'])->name('review.update');
        
        // History
        Route::get('/history', [ConsumerOrderController::class, 'history'])->name('history');
        Route::get('/history/filter/{status?}', [ConsumerOrderController::class, 'history'])->name('history.filter');
        
        // Profile
        Route::get('/profile', [ConsumerProfileController::class, 'edit'])->name('profile');
        Route::get('/profile/edit', [ConsumerProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/update', [ConsumerProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ConsumerProfileController::class, 'updatePassword'])->name('profile.password');
        Route::post('/profile/verify-phone', [ConsumerProfileController::class, 'verifyPhone'])->name('profile.verify-phone');
        Route::post('/profile/resend-otp', [ConsumerProfileController::class, 'resendOtp'])->name('profile.resend-otp');
        Route::put('/preferences/update', [ConsumerProfileController::class, 'updatePreferences'])->name('preferences.update');
        
        // Notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        
        // Favorites
        Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
        Route::post('/favorites/add', [FavoriteController::class, 'store'])->name('favorites.store');
        Route::delete('/favorites/{id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
        
        // Addresses
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::put('/addresses/{id}', [AddressController::class, 'update'])->name('addresses.update');
        Route::delete('/addresses/{id}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::post('/addresses/{id}/default', [AddressController::class, 'setDefault'])->name('addresses.default');
        Route::post('/addresses/{id}/remove-default', [AddressController::class, 'removeDefault'])->name('addresses.remove-default');

        // Chat
        Route::get('/chat/{orderId}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/send/{orderId}', [ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/messages/{orderId}', [ChatController::class, 'fetch'])->name('chat.fetch');

    Route::get('/address-suggestions', [ConsumerOrderController::class, 'addressSuggestions'])
    ->name('order.address-suggestions');

    Route::get('/reverse-geocode', [ConsumerOrderController::class, 'reverseGeocode'])
    ->name('order.reverse-geocode');
    });

    /*
    |--------------------------------------------------------------------------
    | RETAILER ROUTES (Muuza Rejareja)
    |--------------------------------------------------------------------------
    */
    Route::prefix('retailer')->name('retailer.')->group(function () {
        
        Route::get('/dashboard', [RetailerDashboardController::class, 'index'])->name('dashboard');
        
        // Inventory
        Route::get('/inventory', [RetailerInventoryController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/update/{id?}', [RetailerInventoryController::class, 'update'])->name('inventory.update');
        Route::post('/inventory/store', [RetailerInventoryController::class, 'store'])->name('inventory.store');
        Route::delete('/inventory/{id}', [RetailerInventoryController::class, 'destroy'])->name('inventory.destroy');

        // Orders
        Route::get('/orders/incoming', [RetailerOrderController::class, 'incoming'])->name('orders.incoming');
        Route::post('/orders/accept/{id}', [RetailerOrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/reject/{id}', [RetailerOrderController::class, 'reject'])->name('orders.reject');
        Route::get('/orders/active', [RetailerOrderController::class, 'active'])->name('orders.active');
        Route::post('/orders/pickup/{id}', [RetailerOrderController::class, 'pickup'])->name('orders.pickup');
        Route::post('/orders/deliver/{id}', [RetailerOrderController::class, 'deliver'])->name('orders.deliver');
        Route::get('/orders/history', [RetailerOrderController::class, 'history'])->name('orders.history');
        Route::post('/orders/{order}/payment/confirm', [PaymentController::class, 'confirmRetail'])->name('orders.payment.confirm');
        Route::post('/orders/{order}/payment/reject', [PaymentController::class, 'rejectRetail'])->name('orders.payment.reject');

        // Procurement
        Route::get('/procurement/browse', [RetailerProcurementController::class, 'browse'])->name('procurement.browse');
        Route::post('/procurement/add', [RetailerProcurementController::class, 'add'])->name('procurement.add');
        Route::get('/procurement/cart', [RetailerProcurementController::class, 'cart'])->name('procurement.cart');
        Route::get('/procurement/payment-methods/{wholesalerId}', [RetailerProcurementController::class, 'paymentMethods'])->name('procurement.payment-methods');
        Route::put('/procurement/cart/update/{key}', [RetailerProcurementController::class, 'updateCart'])->name('procurement.cart.update');
        Route::delete('/procurement/cart/remove/{key}', [RetailerProcurementController::class, 'removeFromCart'])->name('procurement.cart.remove');
        Route::post('/procurement/checkout', [RetailerProcurementController::class, 'checkout'])->name('procurement.checkout');
        Route::post('/procurement/orders/{order}/pay', [PaymentController::class, 'payWholesale'])->name('procurement.order.pay');
        Route::post('/procurement/orders/{order}/payment-submitted', [PaymentController::class, 'submitWholesaleManualPayment'])->name('procurement.order.payment-submitted');
        Route::get('/procurement/wholesale-orders', [RetailerProcurementController::class, 'wholesaleOrders'])->name('procurement.wholesale_orders');

        // Profile Routes
        Route::get('/profile/edit', [RetailerSettingsController::class, 'profile'])->name('profile.edit');
        Route::put('/profile/update', [RetailerSettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password/update', [RetailerSettingsController::class, 'updatePassword'])->name('password.update');
        Route::put('/preferences/update', [RetailerSettingsController::class, 'updatePreferences'])->name('preferences.update');

        // Shop Settings
        Route::get('/settings/shop', [RetailerSettingsController::class, 'shop'])->name('settings.shop');
        Route::put('/settings/update', [RetailerSettingsController::class, 'update'])->name('settings.update');
        
        // Chat
        Route::get('/chat/{orderId}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/send/{orderId}', [ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/messages/{orderId}', [ChatController::class, 'fetch'])->name('chat.fetch');

        Route::get('/orders/wholesale', [RetailerOrderController::class, 'wholesaleOrders'])->name('orders.wholesale');
Route::get('/orders/wholesale/tracking/{id}', [RetailerOrderController::class, 'wholesaleTracking'])->name('orders.wholesale.tracking');
    });

    /*
    |--------------------------------------------------------------------------
    | WHOLESALER ROUTES (Muuza Jumla)
    |--------------------------------------------------------------------------
    */
    Route::prefix('wholesaler')->name('wholesaler.')->group(function () {
        
        Route::get('/dashboard', [WholesalerDashboardController::class, 'index'])->name('dashboard');
        
        // Products
        Route::get('/products', [WholesalerProductController::class, 'index'])->name('products.index');
        Route::post('/products/store', [WholesalerProductController::class, 'store'])->name('products.store');
        Route::put('/products/{id}/update-price', [WholesalerProductController::class, 'updatePrice'])->name('products.updatePrice');
        Route::put('/products/{id}/add-stock', [WholesalerProductController::class, 'addStock'])->name('products.addStock');
        Route::put('/products/{id}/toggle', [WholesalerProductController::class, 'toggle'])->name('products.toggle');
        Route::delete('/products/{id}', [WholesalerProductController::class, 'destroy'])->name('products.destroy');

        // Orders
        Route::get('/orders/incoming', [WholesalerOrderController::class, 'incoming'])->name('orders.incoming');
        Route::post('/orders/accept/{id}', [WholesalerOrderController::class, 'accept'])->name('orders.accept');
        Route::post('/orders/reject/{id}', [WholesalerOrderController::class, 'reject'])->name('orders.reject');
        Route::get('/orders/processing', [WholesalerOrderController::class, 'processing'])->name('orders.processing');
        Route::post('/orders/process/{id}', [WholesalerOrderController::class, 'process'])->name('orders.process');
        Route::post('/orders/dispatch/{id}', [WholesalerOrderController::class, 'dispatch'])->name('orders.dispatch');
        Route::get('/orders/history', [WholesalerOrderController::class, 'history'])->name('orders.history');
        Route::post('/orders/{order}/payment/confirm', [PaymentController::class, 'confirmWholesale'])->name('orders.payment.confirm');
        Route::post('/orders/{order}/payment/reject', [PaymentController::class, 'rejectWholesale'])->name('orders.payment.reject');

        // Shop Settings
        Route::get('/settings/shop', [WholesalerSettingsController::class, 'shop'])->name('settings.shop');
        Route::get('/settings/profile', [WholesalerSettingsController::class, 'profile'])->name('settings.profile');
        Route::put('/settings/update', [WholesalerSettingsController::class, 'update'])->name('settings.update');
        
        // Account Settings
        Route::get('/account/edit', [WholesalerSettingsController::class, 'account'])->name('account.edit');
        Route::put('/profile/update', [WholesalerSettingsController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password/update', [WholesalerSettingsController::class, 'updatePassword'])->name('password.update');

        // Chat
        Route::get('/chat/{orderId}', [ChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/send/{orderId}', [ChatController::class, 'send'])->name('chat.send');
        Route::get('/chat/messages/{orderId}', [ChatController::class, 'fetch'])->name('chat.fetch');

        Route::post('/orders/deliver/{id}', [WholesalerOrderController::class, 'deliver'])->name('orders.deliver');

        Route::post('/orders/process/{id}', [WholesalerOrderController::class, 'process'])->name('orders.process');
    });
        
    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES (Msimamizi Mkuu) ← SASA ZIKO NDANI YA MIDDLEWARE
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {
        
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users Management
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
        Route::post('/users/{id}/toggle', [AdminUserController::class, 'toggle'])->name('users.toggle');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // Business Verification
        Route::get('/users/verify', [AdminUserController::class, 'verify'])->name('users.verify');
        Route::post('/verify/approve/{id}', [AdminUserController::class, 'approve'])->name('verify.approve');
        Route::post('/verify/reject/{id}', [AdminUserController::class, 'reject'])->name('verify.reject');

        // Pricing
        Route::get('/pricing', [AdminPricingController::class, 'index'])->name('pricing.index');
        Route::post('/pricing/update-products', [AdminPricingController::class, 'updateProducts'])->name('pricing.update.products');
        Route::post('/pricing/update-delivery', [AdminPricingController::class, 'updateDelivery'])->name('pricing.update.delivery');

        // Reports
        Route::get('/reports/finance', [AdminReportController::class, 'finance'])->name('reports.finance');

          // Settings
        Route::get('/settings/profile', [AdminSettingsController::class, 'profile'])->name('settings.profile');
        Route::put('/settings/profile/update', [AdminSettingsController::class, 'update'])->name('settings.profile.update');
        Route::put('/password/update', [AdminSettingsController::class, 'updatePassword'])->name('password.update');
    
          // Phone Verification
        Route::get('/phone/verify', [AdminSettingsController::class, 'showPhoneVerification'])->name('phone.verify');
        Route::post('/phone/verify', [AdminSettingsController::class, 'verifyPhone'])->name('phone.verify.submit');
        Route::post('/phone/resend', [AdminSettingsController::class, 'resendOtp'])->name('phone.resend');

         // Sessions Management
        Route::get('/settings/sessions', [AdminSettingsController::class, 'sessions'])->name('settings.sessions');
        Route::delete('/sessions/{id}', [AdminSettingsController::class, 'destroySession'])->name('sessions.destroy');
        Route::delete('/sessions', [AdminSettingsController::class, 'destroyAllSessions'])->name('sessions.destroy-all');

         // System Logs
        Route::get('/logs', [AdminLogController::class, 'index'])->name('logs.index');

         // Products Management
        Route::post('/products/store', [AdminPricingController::class, 'storeProduct'])->name('products.store');

        // Categories/Brands Management
        Route::post('/categories/store', [AdminPricingController::class, 'storeCategory'])->name('categories.store');

         // Categories Management (Admin)
        Route::get('/categories/{id}/edit', [AdminPricingController::class, 'editCategory'])->name('categories.edit');
        Route::put('/categories/{id}', [AdminPricingController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{id}', [AdminPricingController::class, 'destroyCategory'])->name('categories.destroy');

         // Products Management (Admin)
        Route::get('/products', [AdminPricingController::class, 'productsIndex'])->name('products.index');
        Route::get('/products/{id}/edit', [AdminPricingController::class, 'editProduct'])->name('products.edit');
        Route::put('/products/{id}', [AdminPricingController::class, 'updateProduct'])->name('products.update');
        Route::delete('/products/{id}', [AdminPricingController::class, 'destroyProduct'])->name('products.destroy');

    });
}); // ← HAPA NDIPO MIDDLEWARE GROUP INAFUNGWA
