<?php

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:clear');
    echo "<p>Fully optimized.</p>";
});
Auth::routes();

//Test Routes
Route::get('/demo', 'HomeController@demo')->name('demo'); 

//route for the home
Route::get('/', 'HomeController@index', ['accept' =>['show'], 'index'])->middleware(['impersonate','auth']);
//route for dashboard.index only
Route::get('/dashboard', 'HomeController@index', ['accept' =>['show'], 'index'])->name('home')->middleware(['impersonate','auth']);

//Get Booking form Link
Route::get('bookingforms/{filename}', function($filename){
        $realpath = base_path() . '/uploads'. '/' .$filename;
        return $realpath;    
    });

Route::get('paystack', 'PayController@process');

Route::get('/payment/callback', 'PaymentController@handleGatewayCallback');

Route::get('thanks', function() {
    return view('emails.thankyou');
});

//Export Routes
Route::namespace('Admin')->middleware(['auth'])->group(function(){
    Route::get('export/users', 'UserController@export')->name('user.export');
    //Show email history
    Route::get('updateemails/{id}', 'UserController@emailHistory')->name('updateemails.show');
});


Route::post('/pay', 'PaymentController@redirectToGateway')->name('pay'); 
Route::resource('tests', 'TestsController')->middleware(['impersonate','auth']);
Route::get('userresults', 'TestsController@userresults')->middleware(['impersonate','auth'])->name('tests.results');
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
Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
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

    Route::resource('questions', 'QuestionController');
    Route::resource('modules', 'ModuleController');
    Route::get('enablemodule/{id}', 'ModuleController@enablemodule')->name('modules.enable');
    Route::get('disablemodule/{id}', 'ModuleController@disablemodule')->name('modules.disable');
});
Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('materials', 'MaterialController');
    Route::post('cloneMaterial/{material_id}', 'MaterialController@clone')->name('material.clone');
    Route::get('studymaterials/{filename}', 'MaterialController@getfile');
});

Route::resource('certificates', 'CertificateController')->middleware(['impersonate','auth']);;
   Route::get('certificate/{filename}', 'CertificateController@getfile')->middleware(['impersonate','auth']);
   Route::post('suser', 'CertificateController@selectUser')->name('user.select')->middleware(['impersonate','auth']);

//route for payments history
Route::namespace('Admin')->middleware(['impersonate','auth'])->group(function(){
    Route::resource('payments', 'PaymentController');
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

