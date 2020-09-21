<?php

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    echo "<p>Fully optimized.</p>";
});
Auth::routes();


//route for the home
Route::get('/', 'FrontendController@index')->name('welcome');
Route::get('/trainingimage/{filename}', 'FrontendController@getfile')->name('trainingimage');

Route::get('/trainings/{id}', 'FrontendController@show')->name('trainings');
//route for dashboard.index only
Route::get('/dashboard', 'HomeController@index')->name('home')->middleware(['impersonate','auth']);
Route::get('/training/{p_id}', 'HomeController@trainings')->name('trainings.show')->middleware(['impersonate','auth','programCheck']);

//Get Booking form Link
Route::get('bookingforms/{filename}', function($filename){
        $realpath = base_path() . '/uploads'. '/' .$filename;
        return $realpath;    
    });

// Route::get('paystack', 'PayController@process');
Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

Route::get('thanks', function() {
    return view('emails.thankyou');
})->name('thankyou');


//Upload Payment Evidence

//Export Routes
Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::get('export/users', 'UserController@export')->name('user.export');
    //Show email history
    Route::get('updateemails/{id}', 'UserController@emailHistory')->name('updateemails.show');
});

//upload proof of payment 
Route::resource('pop', 'PopController');
//View proofofpayment
Route::get('view/pop/{filename}', 'PopController@getfile');

//Reconcile route
Route::get('reconcile', 'PopController@reconcile')->name('reconcile');

Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay'); 
Route::resource('tests', 'TestsController')->middleware(['impersonate','auth', 'programCheck']);
Route::resource('mocks', 'MockController')->middleware(['impersonate','auth', 'programCheck']);
Route::get('pretestresults', 'MockController@mockresults')->name('pretest.results');
Route::get('mockuser/{uid}/module/{modid}', 'MockController@grade')->middleware(['impersonate','auth', 'programCheck'])->name('mocks.grade');
Route::get('userresults', 'TestsController@userresults')->middleware(['impersonate','auth','programCheck'])->name('tests.results');
Route::get('mockresults', 'MockController@mockresults')->middleware(['auth'])->name('mocks.results');

Route::resource('profiles', 'ProfileController')->middleware(['impersonate', 'auth']);

Route::resource('scoreSettings', 'ScoreSettingController')->middleware(['auth']);


Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('complains', 'ComplainController');
    Route::get('complainresolved/{complain}', 'ComplainController@resolve')->name('crm.resolved');
});

Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::resource('users', 'UserController');
    //Send Mails
    Route::get('usermail', 'UserController@mails')->name('users.mail');
    Route::post('sendmail', 'UserController@sendmail')->name('user.sendmail');  
});

Route::get('/impersonate/{id}', 'Admin\ImpersonateController@index')->name('impersonate')->middleware('impersonate');
Route::get('/stopimpersonating', 'Admin\ImpersonateController@stopImpersonate')->name('stop.impersonate');
Route::get('/stopimpersonatingfacilitator', 'Admin\ImpersonateController@stopImpersonateFacilitator')->name('stop.impersonate.facilitator');

Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::resource('users', 'UserController');
});

Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::resource('teachers', 'TeacherController');
});
Route::namespace('Admin')->middleware(['impersonate','auth', 'programCheck'])->group(function(){
    Route::resource('results', 'ResultController');
    Route::get('user/{uid}/module/{modid}', 'ResultController@add')->name('results.add');
    Route::get('certifications', 'ResultController@certifications')->name('certifications.index');
    Route::get('resultenable/{id}', 'ResultController@enable')->name('results.enable');
    Route::get('resultdisable/{id}', 'ResultController@disable')->name('results.disable');
});

Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('programs', 'ProgramController');
    Route::get('complainshow/{crm}', 'ProgramController@showcrm')->name('crm.show');
    Route::get('trashed-programs', 'ProgramController@trashed')->name('programs.trashed');
    Route::get('restore/{id}', 'ProgramController@restore')->name('programs.restore');
    Route::get('complainhide/{crm}', 'ProgramController@hidecrm')->name('crm.hide');
    Route::get('close/{id}', 'ProgramController@closeRegistration')->name('registration.close');
    Route::get('open/{id}', 'ProgramController@openRegistration')->name('registration.open');
    Route::get('earlybirdopen/{id}', 'ProgramController@openEarlyBird')->name('earlybird.open');
    Route::get('earlybirdclose/{id}', 'ProgramController@closeEarlyBird')->name('earlybird.close');

    Route::resource('questions', 'QuestionController');
    Route::resource('modules', 'ModuleController');
    Route::get('enablemodule/{id}', 'ModuleController@enablemodule')->name('modules.enable');
    Route::get('disablemodule/{id}', 'ModuleController@disablemodule')->name('modules.disable');
});
Route::namespace('Admin')->middleware(['impersonate','auth', 'programCheck'])->group(function(){
    Route::resource('materials', 'MaterialController');
    Route::post('cloneMaterial/{material_id}', 'MaterialController@clone')->name('material.clone');
    Route::get('studymaterials/{filename}', 'MaterialController@getfile');
});

Route::GET('certificates', 'CertificateController@index')->middleware(['impersonate','auth'])->name('certificates.index');
Route::GET('certificates/create', 'CertificateController@create')->middleware(['impersonate','auth'])->name('certificates.create');
Route::GET('certificate/{filename}', 'CertificateController@getfile')->middleware(['impersonate','auth']);
Route::POST('suser', 'CertificateController@selectUser')->name('user.select');
Route::POST('certificate/save', 'CertificateController@save')->middleware(['impersonate','auth'])->name('certificates.save');
Route::DELETE('certificates/{certificate}', 'CertificateController@destroy')->name('certificates.destroy');
   

//route for payments history
Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('payments', 'PaymentController');
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

