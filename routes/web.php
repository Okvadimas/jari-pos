<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Landing\IndexController    as LandingController;
use App\Http\Controllers\Auth\IndexController       as AuthController;
use App\Http\Controllers\Dashboard\IndexController  as DashboardController;
use App\Http\Controllers\Management\UserController  as UserManagementController;
use App\Http\Controllers\Management\MenuController  as MenuManagementController;
use App\Http\Controllers\Management\RoleController  as RoleManagementController;

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
    Route::get('/user-management',  [UserManagementController::class, 'index'])->name('user-management');
    Route::get('/menu-management',  [MenuManagementController::class, 'index'])->name('menu-management');
    Route::get('/role-management',  [RoleManagementController::class, 'index'])->name('role-management');
    // End Management
    

});
