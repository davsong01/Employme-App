<?php

use App\Settings;

// Route::get('/test', function () {
//     dd(Settings::all());
// });

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
Route::get('/home', 'HomeController@index')->name('home2')->middleware(['impersonate','auth']);

Route::get('/training/{p_id}', 'HomeController@trainings')->name('trainings.show')->middleware(['impersonate','auth','programCheck']);

//Get Booking form Link
Route::get('bookingforms/{filename}', function($filename){
        $realpath = base_path() . '/uploads'. '/' .$filename;
        return $realpath;    
    });

// Route::get('paystack', 'PayController@process');
Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

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



//upload proof of payment 
Route::resource('pop', 'PopController');
//View proofofpayment
Route::get('view/pop/{filename}', 'PopController@getfile');

//Reconcile route
Route::get('reconcile', 'PopController@reconcile')->name('reconcile');
Route::resource('settings', 'SettingsController');

Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay'); 
Route::resource('tests', 'TestsController')->middleware(['impersonate','auth', 'programCheck']);
Route::resource('mocks', 'MockController')->middleware(['impersonate','auth', 'programCheck']);

Route::get('pretestresults', 'MockController@pretest')->name('pretest.select')->middleware(['impersonate','auth','programCheck']);
Route::get('pretestresults/{id}', 'MockController@getgrades')->name('mocks.getgrades')->middleware(['impersonate','auth','programCheck']);

Route::get('mockuser/{uid}/module/{modid}', 'MockController@grade')->middleware(['impersonate','auth', 'programCheck'])->name('mocks.add');
Route::get('userresults', 'TestsController@userresults')->middleware(['impersonate','auth','programCheck'])->name('tests.results');
Route::get('mockresults', 'MockController@mockresults')->middleware(['auth'])->name('mocks.results');

Route::resource('profiles', 'ProfileController')->middleware(['impersonate', 'auth']);

Route::resource('scoreSettings', 'ScoreSettingController')->middleware(['auth']);

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

Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::resource('users', 'UserController');
    Route::get('users/redotest/{id}', 'UserController@redotest')->name('redotest');
    Route::post('users/redotest', 'UserController@saveredotest')->name('saveredotest');
    Route::get('users/stopredotest/{id}', 'UserController@stopredotest')->name('stopredotest');
 
});

Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::resource('teachers', 'TeacherController');
});
Route::namespace('Admin')->middleware(['impersonate','auth', 'programCheck'])->group(function(){
    Route::resource('results', 'ResultController');

    Route::get('postclassresults', 'ResultController@posttest')->name('posttest.results');
    Route::get('postclassresults/{id}', 'ResultController@getgrades')->name('results.getgrades');
    
    Route::get('user/{uid}/module/{modid}/{pid}', 'ResultController@add')->name('results.add');
    Route::get('certifications', 'ResultController@certifications')->name('certifications.index');
    Route::get('resultenable/{id}', 'ResultController@enable')->name('results.enable');
    Route::get('resultdisable/{id}', 'ResultController@disable')->name('results.disable');
});

Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('programs', 'ProgramController');
    Route::resource('locations', 'LocationController');
    Route::get('complainshow/{crm}', 'ProgramController@showcrm')->name('crm.show');
    Route::get('trashed-programs', 'ProgramController@trashed')->name('programs.trashed');
    Route::get('restore/{id}', 'ProgramController@restore')->name('programs.restore');
    Route::get('complainhide/{crm}', 'ProgramController@hidecrm')->name('crm.hide');
    Route::get('close/{id}', 'ProgramController@closeRegistration')->name('registration.close');
    Route::get('open/{id}', 'ProgramController@openRegistration')->name('registration.open');
    Route::get('earlybirdopen/{id}', 'ProgramController@openEarlyBird')->name('earlybird.open');
    Route::get('earlybirdclose/{id}', 'ProgramController@closeEarlyBird')->name('earlybird.close');

    Route::resource('questions', 'QuestionController');
    Route::get('questions/all/{p_id}', 'QuestionController@add')->middleware(['impersonate','auth', 'programCheck'])->name('questions.add');
    Route::get('questionsimport-export/{p_id}', 'QuestionController@importExport')->middleware(['impersonate','auth', 'programCheck'])->name('questions.import');
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



