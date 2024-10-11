<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyUserController;

Route::get('/', [CompanyUserController::class, 'showLoginForm'])->name('company_user.login');
Route::get('/login', [CompanyUserController::class, 'showLoginForm'])->name('company_user.login');
Route::post('/login', [CompanyUserController::class, 'login'])->name('company_user.login.post');
Route::post('/logout', [CompanyUserController::class, 'logout'])->name('company_user.logout');


Route::middleware('auth:company_user')->group(function () {
    Route::get('/dashboard', [CompanyUserController::class, 'dashboard'])->name('company_user.dashboard');

    // Other routes for company users
    Route::get('/profile', [CompanyUserController::class, 'profile'])->name('company_user.profile');
});
