<?php

use App\Transaction;

Route::get('cron/run-utility-tasks', 'UtilityTaskController@runTool');

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    echo "<p>Fully optimized.</p>";
});
Auth::routes();



// Route::get('test', function(){
//     $path = base_path('uploads/certificate_templates/b.jpeg');

//     $image = imagecreatefromjpeg($path);

//     $color = imagecolorallocate($image, 255, 255, 255);
//     $string = 'The string you want to write horizontally on the image';
//     $fontSize = 5;
//     $x = 300;
//     $y = 1000;

//     // write on the image
//     imagestring($image, $fontSize, $x, $y, $string, $color);
//     $fileName = base_path('uploads/certificates/image.jpg');
//     // save the image
//     imagepng($image, $fileName, $quality = 100);
//     dd('done');
// });

// Route::get('test', 'Controller@printTextOnImage');
//route for the home
Route::get( '/reset', 'FrontendController@reset')->name('reset');
Route::get('/correcttransid', function(){
    $transactions = Transaction::whereNull('transid')->get();
    foreach($transactions as $transaction){
        $transaction->update([
            'transid' => $transaction->invoice_id,
        ]);
    }
});

Route::middleware(['template'])->group(function(){
    Route::get('/', 'FrontendController@index')->name('welcome');
    Route::get('/thankyou', 'FrontendController@thankyou')->name('thankyou');    

    Route::get('/trainingimage/{filename}', 'FrontendController@getfile')->name('trainingimage');
    Route::get('/trainings/{id?}', 'FrontendController@show')->name('trainings');
    Route::post('/checkout', 'PaymentController@checkout')->name('checkout'); 
    Route::post('/validate-coupon', 'PaymentController@validateCoupon');
    Route::post('/get-mode-payment-types', 'FrontendController@getModePaymentTypes');
    
    //upload proof of payment 
    Route::resource('pop', 'PopController');
    Route::get('/temp-destroy/{id}', 'PopController@tempDestroy')->name('temp.destroy');

    Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay');
    Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');
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
    Route::get('export/users', 'UserController@export')->name('user.export');
    Route::get('export/participantdetails/{id}', 'ProgramController@exportdetails')->name('program.detailsexport');
    //Show email history
    Route::get('updateemails/{id}', 'UserController@emailHistory')->name('updateemails.show');
});


//View proofofpayment
Route::get('view/pop/{filename}', 'PopController@getfile');

//Reconcile route
Route::get('reconcile', 'PopController@reconcile')->name('reconcile');
Route::resource('settings', 'SettingsController');

Route::resource('tests', 'TestsController')->middleware(['impersonate','auth', 'programCheck']);
Route::resource('mocks', 'MockController')->middleware(['impersonate','auth', 'programCheck']);

Route::get('/training/{p_id}', 'HomeController@trainings')->name('trainings.show')->middleware(['impersonate', 'auth', 'programCheck']);
Route::get('/my-wallet/{user_id}', 'WalletController@participantWalletIndex')->name('my.wallet')->middleware(['impersonate', 'auth']);
Route::post('/top-up-account/{type?}', 'PaymentController@accountTopUp')->name('account.topup')->middleware(['impersonate', 'auth']);
Route::get('/download-program-brochure/{p_id}', 'HomeController@downloadProgramBrochure')->name('download.program.brochure')->middleware(['impersonate', 'auth', 'programCheck']);

Route::get('pretestresults', 'MockController@pretest')->name('pretest.select')->middleware(['impersonate','auth','programCheck']);
Route::any('pretestresults/{id}', 'MockController@getgrades')->name('mocks.getgrades')->middleware(['impersonate','auth','programCheck']);
Route::get('mockuser/{uid}/module/{modid}', 'MockController@grade')->middleware(['impersonate', 'auth', 'programCheck'])->name('mocks.add');
Route::get( 'userresults', 'TestsController@userresults')->middleware(['impersonate', 'auth', 'programCheck'])->name('tests.results');
Route::get('retake-test/{module}', 'TestsController@retakeTest')->middleware(['impersonate', 'auth', 'programCheck'])->name('user.retake.module.test');

Route::get('userresultscomments/{id}', 'TestsController@userResultComments')->middleware(['impersonate', 'auth', 'programCheck'])->name('tests.results.comment');
Route::get('balance-checkout', 'HomeController@balanceCheckout')->name('balance.checkout')->middleware(['impersonate', 'auth', 'programCheck']);

Route::get('training.instructor', 'ProfileController@showFacilitator')->middleware(['impersonate','auth','programCheck'])->name('training.instructor');

Route::get('mockresults', 'MockController@mockresults')->middleware(['auth'])->name('mocks.results');
Route::resource('profiles', 'ProfileController')->middleware(['impersonate', 'auth']);
Route::resource('scoreSettings', 'ScoreSettingController')->middleware(['auth']);

Route::get('selectfacilitator/{id}', 'ProfileController@showFacilitator')->middleware(['impersonate', 'auth']);
Route::POST('savefacilitator', 'ProfileController@saveFacilitator')->name('savefacilitator')->middleware(['impersonate', 'auth']);
Route::get('/dashboard', 'HomeController@index')->name('home')->middleware(['impersonate', 'auth']);
Route::post('/pay-with-account/{type}', 'paymentController@payFromAccount')->name('account.pay')->middleware(['impersonate', 'auth']);

Route::get('/home', 'HomeController@index')->name('home2')->middleware(['impersonate', 'auth']);


Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('complains', 'ComplainController');
    Route::get('complainresolved/{complain}', 'ComplainController@resolve')->name('crm.resolved');
});

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

Route::namespace('Admin')->middleware(['auth', 'impersonate'])->group(function(){
    Route::resource('teachers', 'TeacherController');
    Route::resource('coupon', 'CouponController');
    Route::get('teachers_students/{id}', 'TeacherController@showStudents')->name('teachers.students');
    Route::get('teachers_programs/{id}', 'TeacherController@showPrograms')->name('teachers.programs');
    Route::get('teachers_earnings/{id}', 'TeacherController@showEarnings')->name('teachers.earnings');
});

Route::namespace('Admin')->middleware(['impersonate','auth', 'programCheck'])->group(function(){
    Route::resource('results', 'ResultController');

    Route::get('postclassresults', 'ResultController@posttest')->name('posttest.results');
    Route::any('postclassresults/{id?}', 'ResultController@getgrades')->name('results.getgrades');
    Route::post('waacsp', 'ResultController@verify')->name('send.waacsp');
    
    // Route::get('user/{uid?}/module/{modid?}/{pid?}', 'ResultController@add')->name('results.add');
    Route::get('user/{uid?}/{pid?}', 'ResultController@add')->name('results.add');
    Route::get('certifications', 'ResultController@certifications')->name('certifications.index');
    Route::get('resultenable/{id}', 'ResultController@enable')->name('results.enable');
    Route::get('resultdisable/{id}', 'ResultController@disable')->name('results.disable');
});

Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('programs', 'ProgramController');
    Route::get('training-clone/{training}', 'ProgramController@cloneTraining')->name('training.clone');
    
    Route::resource('locations', 'LocationController');
    Route::get('complainshow/{crm}', 'ProgramController@showcrm')->name('crm.show');
    Route::get('trashed-programs', 'ProgramController@trashed')->name('programs.trashed');
    Route::get('restore/{id}', 'ProgramController@restore')->name('programs.restore');
    Route::get('complainhide/{crm}', 'ProgramController@hidecrm')->name('crm.hide');
    Route::get('close/{id}', 'ProgramController@closeRegistration')->name('registration.close');

    Route::get('open/{id}', 'ProgramController@openRegistration')->name('registration.open');
    Route::get('password-reset/{id}', 'ProgramController@passwordReset')->name('password.reset');
    Route::get('earlybirdopen/{id}', 'ProgramController@openEarlyBird')->name('earlybird.open');
    Route::get('earlybirdclose/{id}', 'ProgramController@closeEarlyBird')->name('earlybird.close');

    Route::resource('questions', 'QuestionController');
    Route::get('questions/all/{p_id}', 'QuestionController@add')->middleware(['impersonate','auth', 'programCheck'])->name('questions.add');
    Route::get('questionsimport-export/{p_id}', 'QuestionController@importExport')->middleware(['impersonate','auth', 'programCheck'])->name('questions.import');
    Route::get('participantsimport/{p_id}', 'UserController@importExport')->middleware(['impersonate','auth', 'programCheck'])->name('training.import');
  
    Route::post('import-training-participant', 'UserController@import')->middleware(['impersonate', 'auth', 'programCheck'])->name('users.import');
    Route::get('download-bulk-user-sample/{filename}', 'UserController@downloadBulkSample')->middleware(['impersonate', 'auth', 'programCheck'])->name('user-bulk-sample');
    Route::get('questions/all/{p_id}', 'QuestionController@add')->middleware(['impersonate', 'auth', 'programCheck'])->name('questions.add');
    
    
    Route::post('import', 'QuestionController@import')->middleware(['impersonate','auth', 'programCheck']);
    Route::post('importquestions', 'QuestionController@import')->middleware(['impersonate','auth', 'programCheck'])->name('questions.import');

    Route::resource('modules', 'ModuleController');
    // Route::post('clonemodule/{mmodule_id}', 'ModuleController@clone')->name('module.showclone');
    Route::post('clonemodule', 'ModuleController@clone')->name('module.clone');
    Route::get('facilitatormodules/{p_id}', 'ModuleController@all')->name('facilitatormodules');
    Route::get('enablemodule/{id}', 'ModuleController@enablemodule')->name('modules.enable');
    Route::get('disablemodule/{id}', 'ModuleController@disablemodule')->name('modules.disable');
});
Route::namespace('Admin')->middleware(['impersonate','auth', 'programCheck'])->group(function(){
    Route::resource('materials', 'MaterialController');
    
    Route::get('materialscreate/{p_id}', 'MaterialController@add')->name('creatematerials');
    Route::get('facilitatormaterials/{p_id}', 'MaterialController@all')->name('facilitatormaterials');
    Route::post('cloneMaterial/{material_id}', 'MaterialController@clone')->name('material.clone');
    Route::get('/studymaterials/{filename}/{p_id}', 'MaterialController@getfile')->name('getmaterial');
});

Route::get('certificates', 'CertificateController@index')->middleware(['impersonate','auth'])->name('certificates.index');
Route::any('generate-auto-certificates/{program_id}', 'CertificateController@generateCertificates')->middleware(['impersonate', 'auth'])->name('certificates.generate');
Route::post('generate-certificate-preview/{program_id}', 'CertificateController@generateCertificatePreview')->middleware(['impersonate', 'auth'])->name('certificates.preview');

Route::get('certificates/create', 'CertificateController@create')->middleware(['impersonate', 'auth'])->name('certificates.create');
Route::post('certificates-modify', 'CertificateController@modify')->middleware(['impersonate','auth'])->name('certificates.modify');
Route::get('certificate/{filename}', 'CertificateController@getfile')->middleware(['impersonate','auth']);
Route::get('suser/{program_id}', 'CertificateController@selectUser')->name('program.select');
Route::POST('certificate/save', 'CertificateController@save')->middleware(['impersonate','auth'])->name('certificates.save');
Route::DELETE('certificates/{certificate}', 'CertificateController@destroy')->name('certificates.destroy');
Route:: get('certificate-status/{user_id}/{program_id}/{status}/{certificate_id}', 'CertificateController@certificateStatus')->name('certificate.status');
Route::get('certificate-clear-duplicate/{program_id}', 'CertificateController@clearDuplicates')->name('certificate.clear.duplicates');



//route for payments history
Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('payments', 'PaymentController');
    
    Route::get('proof-history', 'PaymentController@proofOfPaymentHistory')->name('proof.payment');
    Route::get('payment-history', 'PaymentController@paymentHistory')->name('payments.history');
    Route::get('approve-wallet-transaction\{wallet_id}', 'PaymentController@approveWalletTransaction')->name('approve.wallet.history');
    Route::get('delete-wallet-transaction\{wallet_id}', 'PaymentController@deleteWalletTransaction')->name('delete.wallet.history');
    Route::get('printreceipt/{id}', 'PaymentController@printReceipt')->name('payments.print');
});
Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::resource('pictures', 'PictureController');
});
Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::resource('pictures', 'PictureController');
});
Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::resource('details', 'DetailsController');
});

Route::get('admin-remove-sub-program/{id}', 'Admin\ProgramController@removeSubProgram');






