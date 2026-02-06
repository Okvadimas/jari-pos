<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UtilityController                  as UtilityController;

use App\Http\Controllers\POS\IndexController                as POSController;
use App\Http\Controllers\POS\SyncController                 as POSSyncController;

// Landing
use App\Http\Controllers\Landing\IndexController            as LandingController;

// Auth
use App\Http\Controllers\Auth\IndexController               as AuthController;

// Dashboard
use App\Http\Controllers\Dashboard\IndexController          as DashboardController;

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

// Transaction
use App\Http\Controllers\Transaction\SalesController        as SalesController;
use App\Http\Controllers\Transaction\PurchasingController   as PurchasingController;

Route::get('/', [LandingController::class, 'index'])->name('root');

// PWA Offline Page
Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/login',            [AuthController::class, 'login'])->middleware('redirect-if-authenticated')->name('login');
Route::post('/login',           [AuthController::class, 'processLogin'])->middleware('ajax-request');
Route::get('/register',         [AuthController::class, 'register'])->name('register');
Route::post('/register',        [AuthController::class, 'processRegister']);
Route::get('/reset-password',   [AuthController::class, 'resetPassword'])->name('reset-password');
Route::post('/reset-password',  [AuthController::class, 'processResetPassword']);

Route::group(['middleware' => ['web', 'auth']], function () {

    Route::get('/logout',           [AuthController::class, 'logout'])->name('logout');
    Route::get('/lock-screen',      [AuthController::class, 'lockScreen'])->name('lock-screen');
    Route::post('/unlock-screen',   [AuthController::class, 'unlockScreen'])->name('unlock-screen');

    // Dashboard
    Route::get('/dashboard',        [DashboardController::class, 'index'])->name('dashboard');
    // End Dashboard

    // POS Routes
    Route::group(['prefix' => 'pos', 'middleware' => ['web', 'auth']], function () {
        Route::get('/', [POSController::class, 'index'])->name('pos.index');
        Route::get('/products', [POSController::class, 'getProducts'])->name('pos.products');
        Route::get('/categories', [POSController::class, 'getCategories'])->name('pos.categories');
        Route::get('/top-selling', [POSController::class, 'getTopSelling'])->name('pos.top-selling');
        Route::get('/vouchers', [POSController::class, 'getVouchers'])->name('pos.vouchers');
        Route::post('/store', [POSController::class, 'store'])->name('pos.store');
        Route::post('/sync/transactions', [POSSyncController::class, 'syncTransactions'])->name('pos.sync.transactions');
    });

    // Management
    Route::group(['prefix' => 'management'], function () {
        // User Management (Menu Code: MJ-01)
        Route::group(['middleware' => 'menu-access:MJ-01'], function () {
            Route::get('/user', [UserManagementController::class, 'index'])->name('user-management');
            Route::get('/user/datatable', [UserManagementController::class, 'datatable'])->name('user-management-datatable');
            Route::get('/user/create', [UserManagementController::class, 'create'])->name('user-management-create');
            Route::get('/user/edit/{id}', [UserManagementController::class, 'edit'])->name('user-management-edit');
            Route::post('/user/store', [UserManagementController::class, 'store'])->name('user-management-store');
            Route::post('/user/destroy/{id}', [UserManagementController::class, 'destroy'])->name('user-management-destroy');
        });

        // Role Management (Menu Code: MJ-02)
        Route::group(['middleware' => 'menu-access:MJ-02'], function () {
            Route::get('/akses', [PermissionManagementController::class, 'index'])->name('akses-management');
            Route::get('/akses/datatable', [PermissionManagementController::class, 'datatable'])->name('akses-management-datatable');
            Route::get('/akses/create', [PermissionManagementController::class, 'create'])->name('akses-management-create');
            Route::get('/akses/edit/{id}', [PermissionManagementController::class, 'edit'])->name('akses-management-edit');
            Route::post('/akses/store', [PermissionManagementController::class, 'store'])->name('akses-management-store');
            Route::post('/akses/destroy/{id}', [PermissionManagementController::class, 'destroy'])->name('akses-management-destroy');
        });

        // Company Management (Menu Code: MJ-03)
        Route::group(['middleware' => 'menu-access:MJ-03'], function () {
            Route::get('/company', [CompanyManagementController::class, 'index'])->name('company-management');
            Route::get('/company/datatable', [CompanyManagementController::class, 'datatable'])->name('company-management-datatable');
            Route::get('/company/create', [CompanyManagementController::class, 'create'])->name('company-management-create');
            Route::get('/company/edit/{id}', [CompanyManagementController::class, 'edit'])->name('company-management-edit');
            Route::post('/company/store', [CompanyManagementController::class, 'store'])->name('company-management-store');
            Route::post('/company/destroy/{id}', [CompanyManagementController::class, 'destroy'])->name('company-management-destroy');
        });

        // Payment Management (Menu Code: MJ-04)
        Route::group(['middleware' => 'menu-access:MJ-04'], function () {
            Route::get('/payment', [PaymentManagementController::class, 'index'])->name('management-payment');
            Route::get('/payment/datatable', [PaymentManagementController::class, 'datatable'])->name('management-payment-datatable');
            Route::get('/payment/create', [PaymentManagementController::class, 'create'])->name('management-payment-create');
            Route::get('/payment/edit/{id}', [PaymentManagementController::class, 'edit'])->name('management-payment-edit');
            Route::post('/payment/store', [PaymentManagementController::class, 'store'])->name('management-payment-store');
            Route::post('/payment/destroy/{id}', [PaymentManagementController::class, 'destroy'])->name('management-payment-destroy');
        });

    });
    // End Management
    

    Route::group(['prefix' => 'inventory'], function () {
        // Unit (Menu Code: IN-01)
        Route::group(['middleware' => 'menu-access:IN-01'], function () {
            Route::get('/unit', [UnitController::class, 'index'])->name('inventory-unit');
            Route::get('/unit/datatable', [UnitController::class, 'datatable'])->name('inventory-unit-datatable');
            Route::get('/unit/create', [UnitController::class, 'create'])->name('inventory-unit-create');
            Route::get('/unit/edit/{id}', [UnitController::class, 'edit'])->name('inventory-unit-edit');
            Route::post('/unit/store', [UnitController::class, 'store'])->name('inventory-unit-store');
            Route::post('/unit/destroy/{id}', [UnitController::class, 'destroy'])->name('inventory-unit-destroy');
        });

        // Category (Menu Code: IN-02)
        Route::group(['middleware' => 'menu-access:IN-02'], function () {
            Route::get('/category', [CategoryController::class, 'index'])->name('inventory-category');
            Route::get('/category/datatable', [CategoryController::class, 'datatable'])->name('inventory-category-datatable');
            Route::get('/category/create', [CategoryController::class, 'create'])->name('inventory-category-create');
            Route::get('/category/edit/{id}', [CategoryController::class, 'edit'])->name('inventory-category-edit');
            Route::post('/category/store', [CategoryController::class, 'store'])->name('inventory-category-store');
            Route::post('/category/destroy/{id}', [CategoryController::class, 'destroy'])->name('inventory-category-destroy');
        });

        // Product (Menu Code: IN-03)
        Route::group(['middleware' => 'menu-access:IN-03'], function () {
            Route::get('/product', [ProductController::class, 'index'])->name('inventory-product');
            Route::get('/product/datatable', [ProductController::class, 'datatable'])->name('inventory-product-datatable');
            Route::get('/product/create', [ProductController::class, 'create'])->name('inventory-product-create');
            Route::get('/product/edit/{id}', [ProductController::class, 'edit'])->name('inventory-product-edit');
            Route::post('/product/store', [ProductController::class, 'store'])->name('inventory-product-store');
            Route::post('/product/destroy/{id}', [ProductController::class, 'destroy'])->name('inventory-product-destroy');
        });

        // Product (Menu Code: IN-04)
        Route::group(['middleware' => 'menu-access:IN-04'], function () {
            Route::get('/product-variant', [ProductVariantController::class, 'index'])->name('inventory-product-variant');
            Route::get('/product-variant/datatable', [ProductVariantController::class, 'datatable'])->name('inventory-product-variant-datatable');
            Route::get('/product-variant/create', [ProductVariantController::class, 'create'])->name('inventory-product-variant-create');
            Route::get('/product-variant/edit/{id}', [ProductVariantController::class, 'edit'])->name('inventory-product-variant-edit');
            Route::post('/product-variant/store', [ProductVariantController::class, 'store'])->name('inventory-product-variant-store');
            Route::post('/product-variant/destroy/{id}', [ProductVariantController::class, 'destroy'])->name('inventory-product-variant-destroy');
        });
    });

    // Transaction
    Route::group(['prefix' => 'transaction'], function () {
        Route::get('/sales', [SalesController::class, 'index'])->name('transaction.sales.index');
        Route::get('/sales/datatable', [SalesController::class, 'datatable'])->name('transaction.sales.datatable');
        Route::get('/sales/summary', [SalesController::class, 'summary'])->name('transaction.sales.summary');
        Route::get('/sales/show/{id}', [SalesController::class, 'show'])->name('transaction.sales.show');
        Route::post('/sales/destroy', [SalesController::class, 'destroy'])->name('transaction.sales.destroy');

        Route::get('/purchasing', [PurchasingController::class, 'index'])->name('transaction.purchasing.index');
        Route::get('/purchasing/datatable', [PurchasingController::class, 'datatable'])->name('transaction.purchasing.datatable');
        Route::get('/purchasing/summary', [PurchasingController::class, 'summary'])->name('transaction.purchasing.summary');
        Route::get('/purchasing/show/{id}', [PurchasingController::class, 'show'])->name('transaction.purchasing.show');
        Route::get('/purchasing/create', [PurchasingController::class, 'create'])->name('transaction.purchasing.create');
        Route::get('/purchasing/edit/{id}', [PurchasingController::class, 'edit'])->name('transaction.purchasing.edit');
        Route::post('/purchasing/store', [PurchasingController::class, 'store'])->name('transaction.purchasing.store');
        Route::post('/purchasing/destroy', [PurchasingController::class, 'destroy'])->name('transaction.purchasing.destroy');
    });

    // Utility Routes
    Route::group(['prefix' => 'utility'], function () {
        Route::get('/companies', [UtilityController::class, 'dataCompanies'])->name('utility.companies');
        Route::get('/variants', [UtilityController::class, 'dataProductVariants'])->name('utility.variants');
    });
});
