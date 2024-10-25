<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Company\CompanyUserController;

Route::get('/', [CompanyUserController::class, 'showLoginForm'])->name('company_user.login');
Route::get('/login', [CompanyUserController::class, 'showLoginForm'])->name('company_user.login');
Route::post('/login', [CompanyUserController::class, 'login'])->name('company_user.login.post');
Route::post('/logout', [CompanyUserController::class, 'logout'])->name('company.logout');

Route::middleware('auth:company_user')->group(function () {
    Route::get('dashboard', [CompanyUserController::class, 'dashboard'])->name('company_user.dashboard');
    Route::get('participants', [CompanyUserController::class, 'participants'])->name('company.participants');
    Route::get('profile', [CompanyUserController::class, 'profile'])->name('company_user.profile');
    Route::get('pretestresults', [CompanyUserController::class,'pretest'])->name('company.pretest.select');
    Route::any('pretestresults/{id}', [CompanyUserController::class,'getgrades'])->name('company.mocks.getgrades');

    Route::get('postclassresults', [CompanyUserController::class,'postTest'])->name('company.posttest.results');
    Route::any('postclassresults/{id?}', [CompanyUserController::class,'getPostTesGrades'])->name('company.results.getgrades');
    Route::get('userresults', [CompanyUserController::class, 'userresults'])->name('company.tests.results');
    Route::get('userresultscomments/{id}', [CompanyUserController::class, 'userResultComments'])->name('companytests.results.comment');
});
