<?php

use App\Models\Transaction;
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
use App\Http\Controllers\Admin\CompanyUserController as AdminCompanyUserController;

Route::get('cron/run-utility-tasks', [UtilityTaskController::class, 'runTool']);

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    echo "<p>Fully optimized.</p>";
});

Auth::routes();

//route for the home
Route::get( '/reset', [FrontendController::class, 'reset'])->name('reset');
Route::get('/correcttransid', function(){
    $transactions = Transaction::whereNull('transid')->get();
    foreach($transactions as $transaction){
        $transaction->update([
            'transid' => $transaction->invoice_id,
        ]);
    }
});

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
    Route::resource('pop', PopController::class);
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

Route::get('/thanks', function() {
    return view('emails.thankyou');
})->name('thankyou');


//Export Routes
Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::get('export/users', [UserController::class, 'export'])->name('user.export');
    Route::get('export/participantdetails/{id}', [ProgramController::class, 'exportdetails'])->name('program.detailsexport');
    //Show email history
    Route::get('updateemails/{id}', [UserController::class, 'emailHistory'])->name('updateemails.show');
});

//View proofofpayment
Route::get('view/pop/{filename}', [PopController::class, 'getfile']);

//Reconcile route
Route::get('reconcile', [PopController::class, 'reconcile'])->name('reconcile');
Route::resource('settings', SettingsController::class);

Route::resource('tests', TestsController::class)->middleware(['impersonate','auth', 'programCheck']);

Route::get('/training/{p_id}', [HomeController::class, 'trainings'])->name('trainings.show')->middleware(['impersonate', 'auth', 'programCheck']);
Route::get('/my-wallet/{user_id}', [WalletController::class, 'participantWalletIndex'])->name('my.wallet')->middleware(['impersonate', 'auth']);
Route::post('/top-up-account/{type?}', [PaymentController::class, 'accountTopUp'])->name('account.topup')->middleware(['impersonate', 'auth']);
Route::get('/download-program-brochure/{p_id}', [HomeController::class, 'downloadProgramBrochure'])->name('download.program.brochure')->middleware(['impersonate', 'auth', 'programCheck']);

Route::get('pretestresults', [MockController::class, 'pretest'])->name('pretest.select')->middleware(['impersonate','auth','programCheck']);
Route::any('pretestresults/{id}', [MockController::class, 'getgrades'])->name('mocks.getgrades')->middleware(['impersonate','auth','programCheck']);
Route::get('mockuser/{uid}/module/{modid}', [MockController::class, 'grade'])->middleware(['impersonate', 'auth', 'programCheck'])->name('mocks.add');
Route::get( 'userresults', [TestsController::class, 'userresults'])->middleware(['impersonate', 'auth', 'programCheck'])->name('tests.results');
Route::get('retake-test/{module}', [TestsController::class, 'retakeTest'])->middleware(['impersonate', 'auth', 'programCheck'])->name('user.retake.module.test');

Route::get('userresultscomments/{id}', [TestsController::class, 'userResultComments'])->middleware(['impersonate', 'auth', 'programCheck'])->name('tests.results.comment');
Route::get('balance-checkout', [HomeController::class, 'balanceCheckout'])->name('balance.checkout')->middleware(['impersonate', 'auth', 'programCheck']);

Route::get('training.instructor', [ProfileController::class, 'showFacilitator'])->middleware(['impersonate','auth','programCheck'])->name('training.instructor');

Route::get('mockresults', [MockController::class, 'mockresults'])->middleware(['auth'])->name('mocks.results');
Route::resource('profiles', ProfileController::class)->middleware(['impersonate', 'auth']);
Route::resource('scoreSettings', ScoreSettingController::class)->middleware(['auth']);

Route::get('selectfacilitator/{id}', [ProfileController::class, 'showFacilitator'])->middleware(['impersonate', 'auth']);
Route::POST('savefacilitator', [ProfileController::class, 'saveFacilitator'])->name('savefacilitator')->middleware(['impersonate', 'auth']);
Route::get('/dashboard', [HomeController::class, 'index'])->name('home')->middleware(['impersonate', 'auth']);
Route::post('/pay-with-account/{type}', [paymentController::class, 'payFromAccount'])->name('account.pay')->middleware(['impersonate', 'auth']);

Route::get('/home', [HomeController::class, 'index'])->name('home2')->middleware(['impersonate', 'auth']);


Route::middleware(['impersonate','auth'])->group(function(){
    Route::resource('complains', ComplainController::class);
    Route::get('complainresolved/{complain}', [ComplainController::class, 'resolve'])->name('crm.resolved');
});

Route::middleware(['auth'])->group(function(){
    //Send Mails
    Route::get('usermail', [UserController::class, 'mails'])->name('users.mail');
    Route::post('sendmail', [UserController::class, 'sendmail'])->name('user.sendmail');  
});

Route::get('/impersonate/{id}', [ImpersonateController::class, 'index'])->name('impersonate')->middleware('impersonate');
Route::get('/stopimpersonating', [ImpersonateController::class, 'stopImpersonate'])->name('stop.impersonate');
Route::get('/stopimpersonatingfacilitator', [ImpersonateController::class, 'stopImpersonateFacilitator'])->name('stop.impersonate.facilitator');

Route::middleware(['auth', 'impersonate', 'permission'])->group(function(){
    Route::resource('users', UserController::class);
    Route::resource('payment-modes', PaymentModeController::class);
    Route::resource('paymentmethod', PaymentMethodController::class);
    Route::get('users/redotest/{id}', [UserController::class, 'redotest'])->name('redotest');
    Route::post('users/redotest', [UserController::class, 'saveredotest'])->name('saveredotest');
    Route::get('users/stopredotest/{user_id}/{result_id}', [UserController::class, 'stopredotest'])->name('stopredotest');
});

Route::middleware(['auth', 'impersonate'])->group(function(){
    Route::resource('teachers', TeacherController::class);
    Route::resource('companyuser', AdminCompanyUserController::class);
    Route::resource('coupon', CouponController::class);
    Route::get('teachers_students/{id}', [TeacherController::class, 'showStudents'])->name('teachers.students');
    Route::get('teachers_programs/{id}', [TeacherController::class, 'showPrograms'])->name('teachers.programs');
    Route::get('teachers_earnings/{id}', [TeacherController::class, 'showEarnings'])->name('teachers.earnings');
});

Route::middleware(['impersonate','auth', 'programCheck'])->group(function(){
    Route::resource('results', ResultController::class);

    Route::get('postclassresults', [ResultController::class, 'posttest'])->name('posttest.results');
    Route::any('postclassresults/{id?}', [ResultController::class, 'getgrades'])->name('results.getgrades');
    Route::post('waacsp', [ResultController::class, 'verify'])->name('send.waacsp');
    
    Route::get('user/{uid?}/{pid?}', [ResultController::class, 'add'])->name('results.add');
    Route::get('certifications', [ResultController::class, 'certifications'])->name('certifications.index');
    Route::get('resultenable/{id}', [ResultController::class, 'enable'])->name('results.enable');
    Route::get('resultdisable/{id}', [ResultController::class, 'disable'])->name('results.disable');
});

Route::middleware(['impersonate', 'auth'])->group(function () {
    // Programs Routes
    Route::resource('programs', ProgramController::class);

    Route::controller(ProgramController::class)->group(function () {
        Route::get('training-clone/{training}', 'cloneTraining')->name('training.clone');
        Route::get('complainshow/{crm}', 'showcrm')->name('crm.show');
        Route::get('trashed-programs', 'trashed')->name('programs.trashed');
        Route::get('restore/{id}', 'restore')->name('programs.restore');
        Route::get('complainhide/{crm}', 'hidecrm')->name('crm.hide');
        Route::get('close/{id}', 'closeRegistration')->name('registration.close');
        Route::get('open/{id}', 'openRegistration')->name('registration.open');
        Route::get('password-reset/{id}', 'passwordReset')->name('password.reset');
        Route::get('earlybirdopen/{id}', 'openEarlyBird')->name('earlybird.open');
        Route::get('earlybirdclose/{id}', 'closeEarlyBird')->name('earlybird.close');
    });

    // Locations Routes
    Route::resource('locations', LocationController::class);

    // Questions Routes
    Route::controller(QuestionController::class)->group(function () {
        Route::get('questions/all/{p_id}', 'add')->middleware(['programCheck'])->name('questions.add');
        Route::get('questionsimport-export/{p_id}', 'importExport')->middleware(['programCheck'])->name('questions.import');
        Route::post('import', 'import')->middleware(['programCheck']);
        Route::post('importquestions', 'import')->middleware(['programCheck'])->name('questions.import');
    });

    // Participants Routes
    Route::controller(UserController::class)->group(function () {
        Route::get('participantsimport/{p_id}', 'importExport')->middleware(['programCheck'])->name('training.import');
        Route::post('import-training-participant', 'import')->middleware(['programCheck'])->name('users.import');
        Route::get('download-bulk-user-sample/{filename}', 'downloadBulkSample')->middleware(['programCheck'])->name('user-bulk-sample');
    });

    // Modules Routes
    Route::resource('modules', ModuleController::class);

    Route::controller(ModuleController::class)->group(function () {
        Route::post('clonemodule', 'clone')->name('module.clone');
        Route::get('facilitatormodules/{p_id}', 'all')->name('facilitatormodules');
        Route::get('enablemodule/{id}', 'enablemodule')->name('modules.enable');
        Route::get('disablemodule/{id}', 'disablemodule')->name('modules.disable');
    });

    Route::resource('questions', QuestionController::class);
    Route::resource('modules', ModuleController::class);
    Route::resource('programs', ProgramController::class);
});


Route::middleware(['impersonate', 'auth', 'programCheck'])->group(function () {
    Route::resource('materials', MaterialController::class);

    Route::controller(MaterialController::class)->group(function () {
        Route::get('materialscreate/{p_id}', 'add')->name('creatematerials');
        Route::get('facilitatormaterials/{p_id}', 'all')->name('facilitatormaterials');
        Route::post('cloneMaterial/{material_id}', 'clone')->name('material.clone');
        Route::get('studymaterials/{filename}/{p_id}', 'getfile')->name('getmaterial');
    });
});

Route::middleware(['impersonate', 'auth'])->group(function () {
    Route::controller(CertificateController::class)->group(function () {
        Route::get('certificates', 'index')->name('certificates.index');
        Route::any('generate-auto-certificates/{program_id}', 'generateCertificates')->name('certificates.generate');
        Route::post('generate-certificate-preview/{program_id}', 'generateCertificatePreview')->name('certificates.preview');
        Route::get('certificates/create', 'create')->name('certificates.create');
        Route::post('certificates-modify', 'modify')->name('certificates.modify');
        Route::get('certificate/{filename}', 'getfile');
        Route::get('suser/{program_id}', 'selectUser')->name('program.select');
        Route::post('certificate/save', 'save')->name('certificates.save');
        Route::delete('certificates/{certificate}', 'destroy')->name('certificates.destroy');
        Route::get('certificate-status/{user_id}/{program_id}/{status}/{certificate_id}', 'certificateStatus')->name('certificate.status');
        Route::get('certificate-clear-duplicate/{program_id}', 'clearDuplicates')->name('certificate.clear.duplicates');
    });
});



//route for payments history
Route::middleware(['impersonate', 'auth'])->group(function () {
    Route::resource('payments', AdminPaymentController::class);

    Route::get('proof-history', [AdminPaymentController::class, 'proofOfPaymentHistory'])->name('proof.payment');
    Route::get('payment-history', [AdminPaymentController::class, 'paymentHistory'])->name('payments.history');
    Route::get('approve-wallet-transaction/{wallet_id}', [AdminPaymentController::class, 'approveWalletTransaction'])->name('approve.wallet.history');
    Route::get('delete-wallet-transaction/{wallet_id}', [AdminPaymentController::class, 'deleteWalletTransaction'])->name('delete.wallet.history');
    Route::get('printreceipt/{id}', [AdminPaymentController::class, 'printReceipt'])->name('payments.print');
});

Route::middleware(['auth'])->group(function(){
    Route::resource('pictures', PictureController::class);
});

Route::middleware(['auth'])->group(function(){
    Route::resource('details', DetailsController::class);
});

Route::get('admin-remove-sub-program/{id}', [ProgramController::class, 'removeSubProgram']);






