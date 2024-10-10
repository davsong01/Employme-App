<?php

use App\Transaction;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PopController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MockController;
use App\Http\Controllers\TestsController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\UtilityTaskController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ModuleController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\DetailsController;
use App\Http\Controllers\Admin\PictureController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\CompanyUserController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;


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

//Get Booking form Link
Route::get('bookingforms/{filename}', function ($filename) {
    $realpath = base_path() . '/uploads' . '/' . $filename;
    return $realpath;
});

Route::get('uploads/certificate_previews/{filename}', function ($filename) {
    $realpath = base_path() . '/uploads' . '/' . $filename;
    return $realpath;
});

Route::get('/thanks', function () {
    return view('emails.thankyou');
})->name('thankyou');


Route::middleware(['template'])->group(function () {
    Route::get('/', [FrontendController::class, 'index'])->name('welcome');
    Route::get('/thankyou', [FrontendController::class, 'thankyou'])->name('thankyou');

    Route::get('/trainingimage/{filename}', [FrontendController::class, 'getfile'])->name('trainingimage');
    Route::get('/trainings/{id?}', [FrontendController::class, 'show'])->name('trainings');

    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::post('/validate-coupon', [PaymentController::class, 'validateCoupon']);
    Route::post('/get-mode-payment-types', [FrontendController::class, 'getModePaymentTypes']);

    // Resource for uploading proof of payment
    Route::resource('pop', PopController::class);
    Route::get('/temp-destroy/{id}', [PopController::class, 'tempDestroy'])->name('temp.destroy');

    Route::post('/pay', [PaymentController::class, 'redirectToGateway'])->name('pay');
    Route::get('/payment/callback', [PaymentController::class, 'handleGatewayCallback']);
});

//Export Routes
Route::namespace('Admin')->middleware(['auth'])->group(function () {
    Route::get('export/users', [UserController::class, 'export'])->name('user.export');
    Route::get('export/participantdetails/{id}', [ProgramController::class, 'exportdetails'])->name('program.detailsexport');

    // Show email history
    Route::get('updateemails/{id}', [UserController::class, 'emailHistory'])->name('updateemails.show');
});



//View proofofpayment
Route::get('view/pop/{filename}', [PopController::class, 'getfile']);
//Reconcile route
Route::get('reconcile', [PopController::class, 'reconcile'])->name('reconcile');
Route::resource('settings', SettingsController::class);

Route::middleware(['impersonate', 'auth', 'programCheck'])->group(function () {
    Route::resource('tests', TestsController::class);
    Route::resource('mocks', MockController::class);
    Route::get('/training/{p_id}', [HomeController::class, 'trainings'])->name('trainings.show');
    Route::get('/download-program-brochure/{p_id}', [HomeController::class, 'downloadProgramBrochure'])->name('download.program.brochure');
    Route::get('retake-test/{module}', [TestsController::class, 'retakeTest'])->name('user.retake.module.test');
    Route::get('userresultscomments/{id}', [TestsController::class, 'userResultComments'])->name('tests.results.comment');
    Route::get('pretestresults', [MockController::class, 'pretest'])->name('pretest.select');
    Route::any('pretestresults/{id}', [MockController::class, 'getgrades'])->name('mocks.getgrades');
    Route::get('mockuser/{uid}/module/{modid}', [MockController::class, 'grade'])->name('mocks.add');
    Route::get( 'userresults', [TestsController::class, 'userresults'])->name('tests.results');
    Route::get('balance-checkout', [HomeController::class, 'balanceCheckout'])->name('balance.checkout');
    Route::get('training.instructor', [ProfileController::class, 'showFacilitator'])->name('training.instructor');
});


Route::middleware(['impersonate', 'auth'])->group(function () {
    Route::get('/my-wallet/{user_id}', [WalletController::class, 'participantWalletIndex'])->name('my.wallet');
    Route::post('/top-up-account/{type?}', [PaymentController::class, 'accountTopUp'])->name('account.topup');
    
    Route::resource('profiles', [ProfileController::class]);
    Route::get('selectfacilitator/{id}', [ProfileController::class, 'showFacilitator']);
    Route::POST('savefacilitator', [ProfileController::class, 'saveFacilitator'])->name('savefacilitator');
    Route::post('/pay-with-account/{type}', [paymentController::class, 'payFromAccount'])->name('account.pay');
    
    Route::get('/dashboard', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [HomeController::class, 'index'])->name('home2');
});

Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('complains', 'ComplainController');
    Route::get('complainresolved/{complain}', 'ComplainController@resolve')->name('crm.resolved');
});

Route::get('mockresults', 'MockController@mockresults')->middleware(['auth'])->name('mocks.results');
Route::resource('scoreSettings', 'ScoreSettingController')->middleware(['auth']);

Route::namespace('Admin')->middleware(['auth'])->group(function(){
    //Send Mails
    Route::get('usermail', 'UserController@mails')->name('users.mail');
    Route::post('sendmail', 'UserController@sendmail')->name('user.sendmail');  
});

Route::get('/impersonate/{id}', 'Admin\ImpersonateController@index')->name('impersonate')->middleware('impersonate');
Route::get('/stopimpersonating', 'Admin\ImpersonateController@stopImpersonate')->name('stop.impersonate');
Route::get('/stopimpersonatingfacilitator', 'Admin\ImpersonateController@stopImpersonateFacilitator')->name('stop.impersonate.facilitator');

Route::namespace('Admin')->middleware(['auth', 'impersonate', 'permission'])->group(function(){
    Route::resource('users', 'UserController');
    Route::resource('payment-modes', 'PaymentModeController');
    Route::resource('paymentmethod', 'PaymentMethodController');
    Route::get('users/redotest/{id}', 'UserController@redotest')->name('redotest');
    Route::post('users/redotest', 'UserController@saveredotest')->name('saveredotest');
    Route::get('users/stopredotest/{user_id}/{result_id}', 'UserController@stopredotest')->name('stopredotest');
});

Route::middleware(['auth', 'impersonate'])->namespace('Admin')->group(function () {
    Route::resource('teachers', TeacherController::class);
    Route::resource('companyuser', CompanyUserController::class);
    Route::resource('coupon', CouponController::class);

    Route::get('teachers_students/{id}', [TeacherController::class, 'showStudents'])->name('teachers.students');
    Route::get('teachers_programs/{id}', [TeacherController::class, 'showPrograms'])->name('teachers.programs');
    Route::get('teachers_earnings/{id}', [TeacherController::class, 'showEarnings'])->name('teachers.earnings');
});

Route::middleware(['impersonate', 'auth'])->namespace('Admin')->group(function () {
    Route::resource('programs', ProgramController::class);
    Route::get('training-clone/{training}', [ProgramController::class, 'cloneTraining'])->name('training.clone');

    Route::resource('locations', LocationController::class);
    Route::get('complainshow/{crm}', [ProgramController::class, 'showcrm'])->name('crm.show');
    Route::get('trashed-programs', [ProgramController::class, 'trashed'])->name('programs.trashed');
    Route::get('restore/{id}', [ProgramController::class, 'restore'])->name('programs.restore');
    Route::get('complainhide/{crm}', [ProgramController::class, 'hidecrm'])->name('crm.hide');
    Route::get('close/{id}', [ProgramController::class, 'closeRegistration'])->name('registration.close');
    Route::get('open/{id}', [ProgramController::class, 'openRegistration'])->name('registration.open');
    Route::get('password-reset/{id}', [ProgramController::class, 'passwordReset'])->name('password.reset');
    Route::get('earlybirdopen/{id}', [ProgramController::class, 'openEarlyBird'])->name('earlybird.open');
    Route::get('earlybirdclose/{id}', [ProgramController::class, 'closeEarlyBird'])->name('earlybird.close');

    Route::resource('questions', QuestionController::class);
    Route::get('questions/all/{p_id}', [QuestionController::class, 'add'])
        ->middleware(['programCheck'])
        ->name('questions.add');
    Route::get('questionsimport-export/{p_id}', [QuestionController::class, 'importExport'])
        ->middleware(['programCheck'])
        ->name('questions.import');
    Route::get('participantsimport/{p_id}', [UserController::class, 'importExport'])
        ->middleware(['programCheck'])
        ->name('training.import');

    Route::post('import-training-participant', [UserController::class, 'import'])
        ->middleware(['programCheck'])
        ->name('users.import');
    Route::get('download-bulk-user-sample/{filename}', [UserController::class, 'downloadBulkSample'])
        ->middleware(['programCheck'])
        ->name('user-bulk-sample');

    Route::post('import', [QuestionController::class, 'import'])
        ->middleware(['programCheck']);
    Route::post('importquestions', [QuestionController::class, 'import'])
        ->middleware(['programCheck'])
        ->name('questions.import');

    Route::resource('modules', ModuleController::class);
    Route::post('clonemodule', [ModuleController::class, 'clone'])->name('module.clone');
    Route::get('facilitatormodules/{p_id}', [ModuleController::class, 'all'])->name('facilitatormodules');
    Route::get('enablemodule/{id}', [ModuleController::class, 'enablemodule'])->name('modules.enable');
    Route::get('disablemodule/{id}', [ModuleController::class, 'disablemodule'])->name('modules.disable');
});

Route::middleware(['impersonate', 'auth', 'programCheck'])->namespace('Admin')->group(function () {
    Route::resource('results', ResultController::class);

    Route::get('postclassresults', [ResultController::class, 'posttest'])->name('posttest.results');
    Route::any('postclassresults/{id?}', [ResultController::class, 'getgrades'])->name('results.getgrades');
    Route::post('waacsp', [ResultController::class, 'verify'])->name('send.waacsp');

    Route::get('user/{uid?}/{pid?}', [ResultController::class, 'add'])->name('results.add');
    Route::get('certifications', [ResultController::class, 'certifications'])->name('certifications.index');
    Route::get('resultenable/{id}', [ResultController::class, 'enable'])->name('results.enable');
    Route::get('resultdisable/{id}', [ResultController::class, 'disable'])->name('results.disable');

    Route::resource('materials', MaterialController::class);

    Route::get('materialscreate/{p_id}', [MaterialController::class, 'add'])->name('creatematerials');
    Route::get('facilitatormaterials/{p_id}', [MaterialController::class, 'all'])->name('facilitatormaterials');
    Route::post('cloneMaterial/{material_id}', [MaterialController::class, 'clone'])->name('material.clone');
    Route::get('/studymaterials/{filename}/{p_id}', [MaterialController::class, 'getfile'])->name('getmaterial');
});

Route::middleware(['impersonate', 'auth'])->group(function () {
    Route::get('certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::any('generate-auto-certificates/{program_id}', [CertificateController::class, 'generateCertificates'])->name('certificates.generate');
    Route::post('generate-certificate-preview/{program_id}', [CertificateController::class, 'generateCertificatePreview'])->name('certificates.preview');

    Route::get('certificates/create', [CertificateController::class, 'create'])->name('certificates.create');
    Route::post('certificates-modify', [CertificateController::class, 'modify'])->name('certificates.modify');
    Route::get('certificate/{filename}', [CertificateController::class, 'getfile']);
    Route::get('suser/{program_id}', [CertificateController::class, 'selectUser'])->name('program.select');
    Route::post('certificate/save', [CertificateController::class, 'save'])->name('certificates.save');
    Route::delete('certificates/{certificate}', [CertificateController::class, 'destroy'])->name('certificates.destroy');
    Route::get('certificate-status/{user_id}/{program_id}/{status}/{certificate_id}', [CertificateController::class, 'certificateStatus'])->name('certificate.status');
    Route::get('certificate-clear-duplicate/{program_id}', [CertificateController::class, 'clearDuplicates'])->name('certificate.clear.duplicates');
});

Route::namespace('Admin')->middleware(['impersonate', 'auth'])->group(function () {
    Route::resource('payments', AdminPaymentController::class);

    Route::get('proof-history', [AdminPaymentController::class, 'proofOfPaymentHistory'])->name('proof.payment');
    Route::get('payment-history', [AdminPaymentController::class, 'paymentHistory'])->name('payments.history');
    Route::get('approve-wallet-transaction/{wallet_id}', [AdminPaymentController::class, 'approveWalletTransaction'])->name('approve.wallet.history');
    Route::get('delete-wallet-transaction/{wallet_id}', [AdminPaymentController::class, 'deleteWalletTransaction'])->name('delete.wallet.history');
    Route::get('printreceipt/{id}', [AdminPaymentController::class, 'printReceipt'])->name('payments.print');
});

// Route for pictures
Route::namespace('Admin')->middleware(['auth'])->group(function () {
    Route::resource('pictures', PictureController::class);
});

// Route for details
Route::namespace('Admin')->middleware(['auth'])->group(function () {
    Route::resource('details', DetailsController::class);
});

// Remove sub-program route
Route::get('admin-remove-sub-program/{id}', [ProgramController::class, 'removeSubProgram']);



