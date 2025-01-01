<?php

use App\Models\Transaction;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\PopController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MockController;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\UtilityTaskController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\ScoreSettingController;
use App\Http\Controllers\Admin\DetailsController;
use App\Http\Controllers\Admin\PictureController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ComplainController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ImpersonateController;
use App\Http\Controllers\Admin\PaymentModeController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;


Route::get('cron/run-utility-tasks', [UtilityTaskController::class, 'runTool']);
Route::get('cron/resolve-training-result', [UtilityTaskController::class, 'resolveTrainingResult']);
// Route::get('decode-materials', [MaterialController::class, 'decode']);

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    echo "<p>Fully optimized.</p>";
});

Route::get('/reset', [FrontendController::class, 'reset'])->name('reset');
Route::get('/correcttransid', function () {
    $transactions = Transaction::whereNull('transid')->get();
    foreach ($transactions as $transaction) {
        $transaction->update([
            'transid' => $transaction->invoice_id,
        ]);
    }
});

Route::middleware(['web.access'])->group(function () {
    Auth::routes();
    
    // Guest users
    Route::middleware(['template'])->group(function () {
        Route::controller(FrontendController::class)->group(function () {
            Route::get('/', 'index')->name('welcome');
            Route::get('/thankyou', 'thankyou')->name('thankyou');
            Route::get('/trainingimage/{filename}', 'getfile')->name('trainingimage');
            Route::get('/trainings/{id?}', 'show')->name('trainings');
            Route::post('/get-mode-payment-types', 'getModePaymentTypes');
        });
    
        Route::controller(PaymentController::class)->group(function () {
            Route::post('/checkout', 'checkout')->name('checkout');
            Route::post('/validate-coupon', 'validateCoupon');
            Route::post('/pay', 'redirectToGateway')->name('pay');
            Route::get('/payment/callback', 'handleGatewayCallback');
        });
    
        // Upload proof of payment (POP)
        Route::get('upload-proof-of-payment', [PopController::class, 'create'])->name('upload-proof-of-payment');
        Route::get('/temp-destroy/{id}', [PopController::class, 'tempDestroy'])->name('temp.destroy');
    });
    
    //Get Booking form Link
    Route::get('bookingforms/{filename}', function($filename){
        $realpath = base_path() . '/uploads'. '/' .$filename;
        return $realpath;    
    });
    
    Route::get('uploads/certificate_previews/{filename}', function ($filename) {
        $realpath = base_path() . '/uploads' . '/' . $filename;
        return $realpath;
    });
    
    
    Route::get('uploads/{filename}', function ($filename) {
        $decodedFilename = base64_decode($filename);
        $realpath = base_path('uploads') . '/' . $decodedFilename;
    
        if (!File::exists($realpath)) {
            abort(404, 'File not found');
        }
    
        $mimeType = File::mimeType($realpath);
    
        return response()->file($realpath, [
            'Content-Type' => $mimeType
        ]);
    });
    
    Route::get('/thanks', function() {
        return view('emails.thankyou');
    })->name('thankyou');
    
    // Auth routes
    // Route::get('/impersonate/{id}', [ImpersonateController::class, 'index'])->name('impersonate')->middleware('impersonate');
    // Route::get('/stopimpersonating', [ImpersonateController::class, 'stopImpersonate'])->name('stop.impersonate');
    // Route::get('/stopimpersonatingfacilitator', [ImpersonateController::class, 'stopImpersonateFacilitator'])->name('stop.impersonate.facilitator');
    
    Route::middleware(['auth', 'impersonate','permission'])->group(function () {
        Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
        Route::post('/pay-with-account/{type}', [paymentController::class, 'payFromAccount'])->name('account.pay');
        Route::get('/home', [HomeController::class, 'index'])->name('home2');
    
        Route::resource('tests', TestsController::class)->middleware(['programCheck']);
        Route::resource('mocks', MockController::class)->middleware(['programCheck']);
    
        Route::get('/training/{p_id}', [HomeController::class, 'trainings'])->name('participant.trainings.show')->middleware(['programCheck']);
        Route::get('/my-wallet/{user_id}', [WalletController::class, 'participantWalletIndex'])->name('my.wallet');
        Route::post('/top-up-account/{type?}', [PaymentController::class, 'accountTopUp'])->name('account.topup');
        Route::get('/download-program-brochure/{p_id}', [HomeController::class, 'downloadProgramBrochure'])->name('download.program.brochure')->middleware(['programCheck']);
    
        Route::get('pretestresults', [MockController::class, 'pretest'])->name('pretest.select')->middleware(['programCheck']);
        Route::any('pretestresults/{id}', [MockController::class, 'getgrades'])->name('mocks.getgrades')->middleware(['programCheck']);
        Route::get('mockuser/{uid}/module/{modid}', [MockController::class, 'grade'])->middleware(['programCheck'])->name('mocks.add');
        Route::get('userresults', [TestsController::class, 'userresults'])->middleware(['programCheck'])->name('tests.results');
        Route::get('retake-test/{module}', [TestsController::class, 'retakeTest'])->middleware(['programCheck'])->name('user.retake.module.test');
    
        Route::get('userresultscomments/{id}', [TestsController::class, 'userResultComments'])->middleware(['programCheck'])->name('tests.results.comment');
        Route::get('balance-checkout', [HomeController::class, 'balanceCheckout'])->name('balance.checkout')->middleware(['programCheck']);
    
        Route::get('training.instructor', [ProfileController::class, 'showFacilitator'])->middleware(['programCheck'])->name('training.instructor');
    
        Route::get('mockresults', [MockController::class, 'mockresults'])->middleware(['auth'])->name('mocks.results');
        Route::resource('profiles', ProfileController::class);
        Route::resource('scoreSettings', ScoreSettingController::class)->middleware(['auth']);
    
        Route::get('selectfacilitator/{id}', [ProfileController::class, 'showFacilitator']);
        Route::POST('savefacilitator', [ProfileController::class, 'saveFacilitator'])->name('savefacilitator');
    
        Route::resource('complains', ComplainController::class);
        
        Route::get('crm-program-select/{p_id}', [ComplainController::class, 'getTrainingCrm'])->name('complain.program.select');
        Route::get('complainresolved/{complain}', [ComplainController::class, 'resolve'])->name('crm.resolved');
    
        Route::resource('users', UserController::class);
        Route::resource('payment-modes', PaymentModeController::class);
        Route::resource('paymentmethod', PaymentMethodController::class);
        Route::get('users/redotest/{id}', [UserController::class, 'redotest'])->name('redotest');
        Route::post('users/redotest', [UserController::class, 'saveredotest'])->name('saveredotest');
        Route::get('users/stopredotest/{user_id}/{result_id}', [UserController::class, 'stopredotest'])->name('stopredotest');
        
        Route::middleware(['programCheck'])->group(function(){
            Route::resource('results', ResultController::class);
    
            Route::get('postclassresults', [ResultController::class, 'posttest'])->name('posttest.results');
            Route::any('postclassresults/{id?}', [ResultController::class, 'getgrades'])->name('results.getgrades');
            Route::post('waacsp', [ResultController::class, 'verify'])->name('send.waacsp');
            
            Route::get('user/{id}', [ResultController::class, 'add'])->name('results.add');
            // Route::get('user/{uid?}/{pid?}', [ResultController::class, 'add'])->name('results.add');
            Route::get('certifications', [ResultController::class, 'certifications'])->name('certifications.index');
            Route::get('resultenable/{id}', [ResultController::class, 'enable'])->name('results.enable');
            Route::get('resultdisable/{id}', [ResultController::class, 'disable'])->name('results.disable');
        });

        
        Route::middleware(['programCheck'])->group(function () {
            Route::resource('materials', MaterialController::class);
    
            Route::controller(MaterialController::class)->group(function () {
                Route::get('studymaterials/{filename}/{p_id}', 'getfile')->name('getmaterial');
                Route::get('program-material/{training}', 'getTrainingMaterials')->name('material.program.select');
            });
        });
    
        Route::controller(CertificateController::class)->group(function () {
            Route::get('certificates', 'index')->name('certificates.index');
        });
    });
});
