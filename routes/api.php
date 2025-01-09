<?php

use Illuminate\Http\Request;
use App\Http\Controllers\API\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('cron/run-utility-tasks', [UtilityTaskController::class, 'runTool']);

Route::post('users', [ApiController::class, 'show'])->name('verify');
Route::post('verify-certificate', [ApiController::class, 'verifyCertificateNumber'])->name('verify-certificate');
