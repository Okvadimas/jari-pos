<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Landing\IndexController    as LandingController;
use App\Http\Controllers\Auth\IndexController       as AuthController;
use App\Http\Controllers\Dashboard\IndexController  as DashboardController;
use App\Http\Controllers\Management\UserController  as UserManagementController;
use App\Http\Controllers\Management\AksesController  as AksesManagementController;
use App\Http\Controllers\Management\CompanyController as CompanyController;
use App\Http\Controllers\Management\PaymentController as PaymentController;

// Inventory
use App\Http\Controllers\Inventory\UnitController as UnitController;

Route::get('/', [LandingController::class, 'index'])->name('root');

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

    // Management
    Route::group(['prefix' => 'management'], function () {
        // User Management (Menu Code: MJ-01)
        Route::group(['middleware' => 'menu-access:MJ-01'], function () {
            Route::get('/user',  [UserManagementController::class, 'index'])->name('user-management');
            Route::get('/user/datatable', [UserManagementController::class, 'datatable'])->name('user-management-datatable');
            Route::get('/user/create', [UserManagementController::class, 'create'])->name('user-management-create');
            Route::get('/user/edit/{id}', [UserManagementController::class, 'edit'])->name('user-management-edit');
            Route::post('/user/store', [UserManagementController::class, 'store'])->name('user-management-store');
            Route::post('/user/destroy/{id}', [UserManagementController::class, 'destroy'])->name('user-management-destroy');
        });

        // Role Management (Menu Code: MJ-02)
        Route::group(['middleware' => 'menu-access:MJ-02'], function () {
            Route::get('/akses',  [AksesManagementController::class, 'index'])->name('akses-management');
            Route::get('/akses/datatable', [AksesManagementController::class, 'datatable'])->name('akses-management-datatable');
            Route::get('/akses/create', [AksesManagementController::class, 'create'])->name('akses-management-create');
            Route::get('/akses/edit/{id}', [AksesManagementController::class, 'edit'])->name('akses-management-edit');
            Route::post('/akses/store', [AksesManagementController::class, 'store'])->name('akses-management-store');
            Route::post('/akses/destroy/{id}', [AksesManagementController::class, 'destroy'])->name('akses-management-destroy');
        });

        // Company Management (Menu Code: MJ-03)
        Route::group(['middleware' => 'menu-access:MJ-03'], function () {
            Route::get('/company',  [CompanyController::class, 'index'])->name('company-management');
            Route::get('/company/datatable', [CompanyController::class, 'datatable'])->name('company-management-datatable');
            Route::get('/company/create', [CompanyController::class, 'create'])->name('company-management-create');
            Route::post('/company/store', [CompanyController::class, 'store'])->name('company-management-store');
            Route::get('/company/edit/{id}', [CompanyController::class, 'edit'])->name('company-management-edit');
            Route::post('/company/update/{id}', [CompanyController::class, 'update'])->name('company-management-update');
            Route::post('/company/destroy/{id}', [CompanyController::class, 'destroy'])->name('company-management-destroy');
        });

        // Payment Management (Menu Code: MJ-04)
        Route::group(['middleware' => 'menu-access:MJ-04'], function () {
            Route::get('/payment',  [PaymentController::class, 'index'])->name('management-payment');
            Route::get('/payment/datatable', [PaymentController::class, 'datatable'])->name('management-payment-datatable');
            Route::get('/payment/create', [PaymentController::class, 'create'])->name('management-payment-create');
            Route::post('/payment/store', [PaymentController::class, 'store'])->name('management-payment-store');
            Route::get('/payment/edit/{id}', [PaymentController::class, 'edit'])->name('management-payment-edit');
            Route::post('/payment/update/{id}', [PaymentController::class, 'update'])->name('management-payment-update');
            Route::post('/payment/destroy/{id}', [PaymentController::class, 'destroy'])->name('management-payment-destroy');
        });

    });
    // End Management
    

    Route::group(['prefix' => 'inventory'], function () {
        // Unit (Menu Code: IN-01)
        Route::group(['middleware' => 'menu-access:IN-01'], function () {
            Route::get('/unit',  [UnitController::class, 'index'])->name('inventory-unit');
            Route::get('/unit/datatable', [UnitController::class, 'datatable'])->name('inventory-unit-datatable');
            Route::get('/unit/create', [UnitController::class, 'create'])->name('inventory-unit-create');
            Route::post('/unit/store', [UnitController::class, 'store'])->name('inventory-unit-store');
            Route::get('/unit/edit/{id}', [UnitController::class, 'edit'])->name('inventory-unit-edit');
            Route::post('/unit/update/{id}', [UnitController::class, 'update'])->name('inventory-unit-update');
            Route::post('/unit/destroy/{id}', [UnitController::class, 'destroy'])->name('inventory-unit-destroy');
        });
    })
});
