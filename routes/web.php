<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Utilities\UtilityController;

use App\Http\Controllers\POS\PosController                   as POSController;
use App\Http\Controllers\POS\SyncController                  as POSSyncController;

// Landing
use App\Http\Controllers\Landing\LandingController;

// Auth
use App\Http\Controllers\Auth\AuthController;

// Dashboard
use App\Http\Controllers\Dashboard\DashboardController;

// Management
use App\Http\Controllers\Management\UserController          as UserManagementController;
use App\Http\Controllers\Management\PermissionController    as PermissionManagementController;
use App\Http\Controllers\Management\CompanyController       as CompanyManagementController;
use App\Http\Controllers\Management\PaymentController       as PaymentManagementController;

// Inventory
use App\Http\Controllers\Inventory\UnitController           as UnitController;
use App\Http\Controllers\Inventory\CategoryController       as CategoryController;
use App\Http\Controllers\Inventory\ProductController        as ProductController;
use App\Http\Controllers\Inventory\ProductVariantController as ProductVariantController;
use App\Http\Controllers\Inventory\StockOpnameController    as StockOpnameController;

// Transaction
use App\Http\Controllers\Transaction\SalesController        as SalesController;
use App\Http\Controllers\Transaction\PurchasingController   as PurchasingController;

// Report
use App\Http\Controllers\Report\RecommendationController   as RecommendationController;

// Chatbot
use App\Http\Controllers\Chatbot\ChatbotController         as ChatbotController;

// Finance
use App\Http\Controllers\Finance\BusinessExpenseController;
use App\Http\Controllers\Finance\AppSaleController;
use App\Http\Controllers\Finance\AffiliateCommissionController;
use App\Http\Controllers\Finance\DiscountCouponController;
use App\Http\Controllers\Finance\AffiliateDashboardController;

Route::get('/', [LandingController::class, 'index'])->name('root');

// PWA Offline Page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/login',            [AuthController::class, 'login'])->middleware('redirect-if-authenticated')->name('login');
Route::post('/login',           [AuthController::class, 'processLogin'])->middleware(['ajax-request', 'throttle:5,1']);
Route::get('/register',         [AuthController::class, 'register'])->name('register');
Route::post('/register',        [AuthController::class, 'processRegister']);
Route::get('/email/verify',     [AuthController::class, 'verifyNotice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('verification.verify');
Route::post('/email/verification-resend', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1')->name('verification.resend');
Route::get('/reset-password',   [AuthController::class, 'resetPassword'])->name('reset-password');
Route::post('/reset-password',  [AuthController::class, 'processResetPassword'])->middleware('throttle:5,1');

// Auth routes (no email verification required)
Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/logout',           [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile',          [AuthController::class, 'myProfile'])->name('profile');
    Route::get('/profile/data',     [AuthController::class, 'getProfileData'])->name('profile.data');
    Route::post('/profile/update',  [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/update-picture', [AuthController::class, 'updateProfilePicture'])->name('profile.update-picture');
    Route::get('/lock-screen',      [AuthController::class, 'lockScreen'])->name('lock-screen');
    Route::post('/unlock-screen',   [AuthController::class, 'unlockScreen'])->name('unlock-screen');
});

// Auth + Verified routes (email verification required)
Route::group(['middleware' => ['web', 'auth', 'verified', 'screen.unlocked']], function () {

    // Dashboard
    Route::get('/dashboard',        [DashboardController::class, 'index'])->name('dashboard');
    // End Dashboard

    // Change Password
    Route::get('/change-password',  [AuthController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'processChangePassword'])->name('change-password.process');

    // POS Routes
    Route::group(['prefix' => 'pos', 'middleware' => ['web', 'auth']], function () {
        Route::get('/', [POSController::class, 'index'])->name('pos.index');
        Route::get('/products', [POSController::class, 'getProducts'])->name('pos.products');
        Route::get('/categories', [POSController::class, 'getCategories'])->name('pos.categories');
        Route::get('/top-selling', [POSController::class, 'getTopSelling'])->name('pos.top-selling');
        Route::get('/vouchers', [POSController::class, 'getVouchers'])->name('pos.vouchers');
        Route::post('/store', [POSController::class, 'store'])->name('pos.store');
        Route::get('/print/{id}', [POSController::class, 'printReceipt'])->name('pos.print');
        Route::get('/test-receipt', [POSController::class, 'testReceipt'])->name('pos.test-receipt');
        Route::get('/test-receipt-2', [POSController::class, 'testReceipt2'])->name('pos.test-receipt-2');
        Route::post('/sync/transactions', [POSSyncController::class, 'syncTransactions'])->name('pos.sync.transactions');
        Route::get('/receipt-data/{id}', [POSController::class, 'getReceiptData'])->name('pos.receipt-data');
        Route::get('/transactions', [POSController::class, 'getTransactionHistory'])->name('pos.transactions');

    });

    // Management
    Route::group(['prefix' => 'management'], function () {
        // User Management (Menu Code: MJ-01)
        Route::group(['middleware' => 'menu-access:MJ-01'], function () {
            Route::get('/user', [UserManagementController::class, 'index'])->name('management.user.index');
            Route::post('/user/datatable', [UserManagementController::class, 'datatable'])->name('management.user.datatable');
            Route::get('/user/create', [UserManagementController::class, 'create'])->name('management.user.create');
            Route::get('/user/edit/{id}', [UserManagementController::class, 'edit'])->name('management.user.edit');
            Route::post('/user/store', [UserManagementController::class, 'store'])->name('management.user.store');
            Route::post('/user/destroy/{id}', [UserManagementController::class, 'destroy'])->name('management.user.destroy');
        });

        // Role Management (Menu Code: MJ-02)
        Route::group(['middleware' => 'menu-access:MJ-02'], function () {
            Route::get('/akses', [PermissionManagementController::class, 'index'])->name('management.permission.index');
            Route::post('/akses/datatable', [PermissionManagementController::class, 'datatable'])->name('management.permission.datatable');
            Route::get('/akses/create', [PermissionManagementController::class, 'create'])->name('management.permission.create');
            Route::get('/akses/edit/{id}', [PermissionManagementController::class, 'edit'])->name('management.permission.edit');
            Route::post('/akses/store', [PermissionManagementController::class, 'store'])->name('management.permission.store');
            Route::post('/akses/destroy/{id}', [PermissionManagementController::class, 'destroy'])->name('management.permission.destroy');
        });

        // Company Management (Menu Code: MJ-03)
        Route::group(['middleware' => 'menu-access:MJ-03'], function () {
            Route::get('/company', [CompanyManagementController::class, 'index'])->name('management.company.index');
            Route::post('/company/datatable', [CompanyManagementController::class, 'datatable'])->name('management.company.datatable');
            Route::get('/company/create', [CompanyManagementController::class, 'create'])->name('management.company.create');
            Route::get('/company/edit/{id}', [CompanyManagementController::class, 'edit'])->name('management.company.edit');
            Route::post('/company/store', [CompanyManagementController::class, 'store'])->name('management.company.store');
            Route::post('/company/destroy/{id}', [CompanyManagementController::class, 'destroy'])->name('management.company.destroy');
        });

        // Payment Management (Menu Code: MJ-04)
        Route::group(['middleware' => 'menu-access:MJ-04'], function () {
            Route::get('/payment', [PaymentManagementController::class, 'index'])->name('management.payment.index');
            Route::post('/payment/datatable', [PaymentManagementController::class, 'datatable'])->name('management.payment.datatable');
            Route::get('/payment/create', [PaymentManagementController::class, 'create'])->name('management.payment.create');
            Route::get('/payment/edit/{id}', [PaymentManagementController::class, 'edit'])->name('management.payment.edit');
            Route::post('/payment/store', [PaymentManagementController::class, 'store'])->name('management.payment.store');
            Route::post('/payment/destroy/{id}', [PaymentManagementController::class, 'destroy'])->name('management.payment.destroy');
        });

    });
    // End Management
    

    Route::group(['prefix' => 'inventory'], function () {
        // Unit (Menu Code: IN-01)
        Route::group(['middleware' => 'menu-access:IN-01'], function () {
            Route::get('/unit', [UnitController::class, 'index'])->name('inventory.unit.index');
            Route::post('/unit/datatable', [UnitController::class, 'datatable'])->name('inventory.unit.datatable');
            Route::get('/unit/create', [UnitController::class, 'create'])->name('inventory.unit.create');
            Route::get('/unit/edit/{id}', [UnitController::class, 'edit'])->name('inventory.unit.edit');
            Route::post('/unit/store', [UnitController::class, 'store'])->name('inventory.unit.store');
            Route::post('/unit/destroy/{id}', [UnitController::class, 'destroy'])->name('inventory.unit.destroy');
        });

        // Category (Menu Code: IN-02)
        Route::group(['middleware' => 'menu-access:IN-02'], function () {
            Route::get('/category', [CategoryController::class, 'index'])->name('inventory.category.index');
            Route::post('/category/datatable', [CategoryController::class, 'datatable'])->name('inventory.category.datatable');
            Route::get('/category/create', [CategoryController::class, 'create'])->name('inventory.category.create');
            Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('inventory.category.edit');
            Route::post('/category/store', [CategoryController::class, 'store'])->name('inventory.category.store');
            Route::post('/category/destroy/{id}', [CategoryController::class, 'destroy'])->name('inventory.category.destroy');
        });

        // Product (Menu Code: IN-03)
        Route::group(['middleware' => 'menu-access:IN-03'], function () {
            Route::get('/product', [ProductController::class, 'index'])->name('inventory.product.index');
            Route::post('/product/datatable', [ProductController::class, 'datatable'])->name('inventory.product.datatable');
            Route::get('/product/create', [ProductController::class, 'create'])->name('inventory.product.create');
            Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('inventory.product.edit');
            Route::post('/product/store', [ProductController::class, 'store'])->name('inventory.product.store');
            Route::post('/product/destroy/{id}', [ProductController::class, 'destroy'])->name('inventory.product.destroy');
        });

        // Product (Menu Code: IN-04)
        Route::group(['middleware' => 'menu-access:IN-04'], function () {
            Route::get('/product-variant', [ProductVariantController::class, 'index'])->name('inventory.product-variant.index');
            Route::post('/product-variant/datatable', [ProductVariantController::class, 'datatable'])->name('inventory.product-variant.datatable');
            Route::get('/product-variant/create', [ProductVariantController::class, 'create'])->name('inventory.product-variant.create');
            Route::get('/product-variant/edit/{id}', [ProductVariantController::class, 'edit'])->name('inventory.product-variant.edit');
            Route::post('/product-variant/store', [ProductVariantController::class, 'store'])->name('inventory.product-variant.store');
            Route::post('/product-variant/destroy/{id}', [ProductVariantController::class, 'destroy'])->name('inventory.product-variant.destroy');
        });

        // Stock Opname (Menu Code: IN-05)
        Route::group(['middleware' => 'menu-access:IN-05'], function () {
            Route::get('/stock-opname', [StockOpnameController::class, 'index'])->name('inventory.stock-opname.index');
            Route::post('/stock-opname/datatable', [StockOpnameController::class, 'datatable'])->name('inventory.stock-opname.datatable');
            Route::get('/stock-opname/summary', [StockOpnameController::class, 'summary'])->name('inventory.stock-opname.summary');
            Route::get('/stock-opname/create', [StockOpnameController::class, 'create'])->name('inventory.stock-opname.create');
            Route::get('/stock-opname/edit/{id}', [StockOpnameController::class, 'edit'])->name('inventory.stock-opname.edit');
            Route::get('/stock-opname/show/{id}', [StockOpnameController::class, 'show'])->name('inventory.stock-opname.show');
            Route::get('/stock-opname/system-stock/{productVariantId}', [StockOpnameController::class, 'getSystemStock'])->name('inventory.stock-opname.system-stock');
            Route::post('/stock-opname/store', [StockOpnameController::class, 'store'])->name('inventory.stock-opname.store');
            Route::post('/stock-opname/approve/{id}', [StockOpnameController::class, 'approve'])->name('inventory.stock-opname.approve');
            Route::post('/stock-opname/cancel/{id}', [StockOpnameController::class, 'cancel'])->name('inventory.stock-opname.cancel');
            Route::post('/stock-opname/destroy', [StockOpnameController::class, 'destroy'])->name('inventory.stock-opname.destroy');
        });
    });

    // Transaction
    Route::group(['prefix' => 'transaction'], function () {
        Route::get('/sales', [SalesController::class, 'index'])->name('transaction.sales.index');
        Route::post('/sales/datatable', [SalesController::class, 'datatable'])->name('transaction.sales.datatable');
        Route::get('/sales/summary', [SalesController::class, 'summary'])->name('transaction.sales.summary');
        Route::get('/sales/show/{id}', [SalesController::class, 'show'])->name('transaction.sales.show');
        Route::get('/sales/edit/{id}', [SalesController::class, 'edit'])->name('transaction.sales.edit');
        Route::post('/sales/store', [SalesController::class, 'store'])->name('transaction.sales.store');
        Route::post('/sales/destroy', [SalesController::class, 'destroy'])->name('transaction.sales.destroy');

        Route::get('/purchasing', [PurchasingController::class, 'index'])->name('transaction.purchasing.index');
        Route::post('/purchasing/datatable', [PurchasingController::class, 'datatable'])->name('transaction.purchasing.datatable');
        Route::get('/purchasing/summary', [PurchasingController::class, 'summary'])->name('transaction.purchasing.summary');
        Route::get('/purchasing/show/{id}', [PurchasingController::class, 'show'])->name('transaction.purchasing.show');
        Route::get('/purchasing/create', [PurchasingController::class, 'create'])->name('transaction.purchasing.create');
        Route::get('/purchasing/edit/{id}', [PurchasingController::class, 'edit'])->name('transaction.purchasing.edit');
        Route::post('/purchasing/store', [PurchasingController::class, 'store'])->name('transaction.purchasing.store');
        Route::post('/purchasing/destroy', [PurchasingController::class, 'destroy'])->name('transaction.purchasing.destroy');
    });

    // Report
    Route::group(['prefix' => 'report'], function () {
        Route::get('/stock-recommendation', [RecommendationController::class, 'index'])->name('report.stock-recommendation');
        Route::get('/stock-recommendation/detail/{id?}', [RecommendationController::class, 'detail'])->name('report.stock-recommendation.detail');
        Route::get('/stock-recommendation/form/{id?}', [RecommendationController::class, 'form'])->name('report.stock-recommendation.form');
        Route::post('/stock-recommendation/generate', [RecommendationController::class, 'generate'])->name('report.stock-recommendation.generate');
        Route::get('/stock-recommendation/datatable', [RecommendationController::class, 'datatable'])->name('report.stock-recommendation.datatable');
        Route::get('/stock-recommendation/summary/{id?}', [RecommendationController::class, 'summary'])->name('report.stock-recommendation.summary');
        Route::post('/stock-recommendation/update-qty', [RecommendationController::class, 'updateQty'])->name('report.stock-recommendation.update-qty');
        Route::post('/stock-recommendation/save/{id}', [RecommendationController::class, 'save'])->name('report.stock-recommendation.save');
        Route::get('/stock-recommendation/download-pdf/{id}', [RecommendationController::class, 'downloadPdf'])->name('report.stock-recommendation.download-pdf');
        Route::get('/stock-recommendation/ai/{id}', [RecommendationController::class, 'getAiRecommendations'])->name('report.stock-recommendation.ai');
        Route::delete('/stock-recommendation/destroy/{id}', [RecommendationController::class, 'destroy'])->name('report.stock-recommendation.destroy');
    });

    // Chatbot
    Route::group(['prefix' => 'chatbot', 'middleware' => 'menu-access:CB-01'], function () {
        Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
        Route::post('/ask', [ChatbotController::class, 'ask'])->name('chatbot.ask');
        Route::post('/upload', [ChatbotController::class, 'uploadDocument'])->name('chatbot.upload');
        Route::get('/documents', [ChatbotController::class, 'listDocuments'])->name('chatbot.documents');
        Route::delete('/document/{id}', [ChatbotController::class, 'deleteDocument'])->name('chatbot.delete-document');
    });

    // Finance / Keuangan
    Route::group(['prefix' => 'finance'], function () {
        // Pengeluaran Bisnis (Menu Code: KU-01)
        Route::group(['middleware' => 'menu-access:KU-01'], function () {
            Route::get('/business-expense', [BusinessExpenseController::class, 'index'])->name('finance.business-expense.index');
            Route::post('/business-expense/datatable', [BusinessExpenseController::class, 'datatable'])->name('finance.business-expense.datatable');
            Route::get('/business-expense/summary', [BusinessExpenseController::class, 'summary'])->name('finance.business-expense.summary');
            Route::get('/business-expense/create', [BusinessExpenseController::class, 'create'])->name('finance.business-expense.create');
            Route::get('/business-expense/edit/{id}', [BusinessExpenseController::class, 'edit'])->name('finance.business-expense.edit');
            Route::get('/business-expense/show/{id}', [BusinessExpenseController::class, 'show'])->name('finance.business-expense.show');
            Route::post('/business-expense/store', [BusinessExpenseController::class, 'store'])->name('finance.business-expense.store');
            Route::post('/business-expense/destroy', [BusinessExpenseController::class, 'destroy'])->name('finance.business-expense.destroy');
        });

        // Penjualan Aplikasi (Menu Code: KU-02)
        Route::group(['middleware' => 'menu-access:KU-02'], function () {
            Route::get('/app-sale', [AppSaleController::class, 'index'])->name('finance.app-sale.index');
            Route::post('/app-sale/datatable', [AppSaleController::class, 'datatable'])->name('finance.app-sale.datatable');
            Route::get('/app-sale/summary', [AppSaleController::class, 'summary'])->name('finance.app-sale.summary');
            Route::get('/app-sale/create', [AppSaleController::class, 'create'])->name('finance.app-sale.create');
            Route::get('/app-sale/edit/{id}', [AppSaleController::class, 'edit'])->name('finance.app-sale.edit');
            Route::get('/app-sale/show/{id}', [AppSaleController::class, 'show'])->name('finance.app-sale.show');
            Route::post('/app-sale/store', [AppSaleController::class, 'store'])->name('finance.app-sale.store');
            Route::post('/app-sale/confirm/{id}', [AppSaleController::class, 'confirm'])->name('finance.app-sale.confirm');
            Route::post('/app-sale/destroy', [AppSaleController::class, 'destroy'])->name('finance.app-sale.destroy');
        });

        // Komisi Affiliate (Menu Code: KU-03)
        Route::group(['middleware' => 'menu-access:KU-03'], function () {
            Route::get('/affiliate-commission', [AffiliateCommissionController::class, 'index'])->name('finance.affiliate-commission.index');
            Route::post('/affiliate-commission/datatable', [AffiliateCommissionController::class, 'datatable'])->name('finance.affiliate-commission.datatable');
            Route::get('/affiliate-commission/summary', [AffiliateCommissionController::class, 'summary'])->name('finance.affiliate-commission.summary');
            Route::get('/affiliate-commission/show/{id}', [AffiliateCommissionController::class, 'show'])->name('finance.affiliate-commission.show');
            Route::post('/affiliate-commission/pay/{id}', [AffiliateCommissionController::class, 'markAsPaid'])->name('finance.affiliate-commission.pay');
            Route::post('/affiliate-commission/cancel/{id}', [AffiliateCommissionController::class, 'cancel'])->name('finance.affiliate-commission.cancel');
        });

        // Kupon Diskon (Menu Code: KU-04)
        Route::group(['middleware' => 'menu-access:KU-04'], function () {
            Route::get('/discount-coupon', [DiscountCouponController::class, 'index'])->name('finance.discount-coupon.index');
            Route::post('/discount-coupon/datatable', [DiscountCouponController::class, 'datatable'])->name('finance.discount-coupon.datatable');
            Route::get('/discount-coupon/summary', [DiscountCouponController::class, 'summary'])->name('finance.discount-coupon.summary');
            Route::get('/discount-coupon/create', [DiscountCouponController::class, 'create'])->name('finance.discount-coupon.create');
            Route::get('/discount-coupon/edit/{id}', [DiscountCouponController::class, 'edit'])->name('finance.discount-coupon.edit');
            Route::post('/discount-coupon/store', [DiscountCouponController::class, 'store'])->name('finance.discount-coupon.store');
            Route::post('/discount-coupon/destroy', [DiscountCouponController::class, 'destroy'])->name('finance.discount-coupon.destroy');
        });

        // Dashboard Affiliate (Menu Code: KU-05)
        Route::group(['middleware' => 'menu-access:KU-05'], function () {
            Route::get('/affiliate-dashboard', [AffiliateDashboardController::class, 'index'])->name('finance.affiliate-dashboard.index');
            Route::post('/affiliate-dashboard/datatable', [AffiliateDashboardController::class, 'datatable'])->name('finance.affiliate-dashboard.datatable');
            Route::get('/affiliate-dashboard/summary', [AffiliateDashboardController::class, 'summary'])->name('finance.affiliate-dashboard.summary');
            Route::post('/affiliate-dashboard/detail/{code}', [AffiliateDashboardController::class, 'detail'])->name('finance.affiliate-dashboard.detail');
        });
    });

    // Utility Routes
    Route::group(['prefix' => 'utility'], function () {
        Route::get('/companies', [UtilityController::class, 'dataCompanies'])->name('utility.companies');
        Route::get('/variants', [UtilityController::class, 'dataProductVariants'])->name('utility.variants');
        Route::get('/payment-methods', [UtilityController::class, 'dataPaymentMethods'])->name('utility.payment-methods');
    });
});
