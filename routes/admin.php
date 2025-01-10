<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PopController;
use App\Http\Controllers\MockController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\CertificateController;
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

Route::middleware(['admin.access'])->group(function () {
    Route::get('/', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::get('/ad-login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin-login', [AdminController::class, 'login'])->name('admin.login.post');
    Route::post('/admin-logout', [AdminController::class, 'logout'])->name('admin.logout');

    Route::get('impersonate-staff/{id}', [ImpersonateController::class, 'indexStaff'])->name('admin.impersonate')->middleware('impersonate.admin');
    Route::get('stopimpersonating-staff', [ImpersonateController::class, 'stopImpersonateFacilitator'])->name('stop.impersonate.admin');
    

    Route::middleware(['auth.admin', 'impersonate.admin', 'permission'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.home');
        Route::resource('users', UserController::class);

        Route::resource('teachers', TeacherController::class);
        Route::resource('companyuser', AdminCompanyUserController::class);
        Route::resource('coupon', CouponController::class);
        Route::get('teachers_students/{id}', [TeacherController::class, 'showStudents'])->name('teachers.students');
        Route::get('teachers_programs/{id}', [TeacherController::class, 'showPrograms'])->name('teachers.programs');
        Route::get('teachers_earnings/{id}', [TeacherController::class, 'showEarnings'])->name('teachers.earnings');
    

        Route::resource('payment-modes', PaymentModeController::class);
        Route::resource('paymentmethod', PaymentMethodController::class);
        Route::get('users/redotest/{id}', [UserController::class, 'redotest'])->name('redotest');
        Route::post('users/redotest', [UserController::class, 'saveredotest'])->name('saveredotest');
        Route::get('users/stopredotest/{user_id}/{result_id}', [UserController::class, 'stopredotest'])->name('stopredotest');
    

        //Send Mails
        Route::get('usermail', [UserController::class, 'mails'])->name('users.mail');
        Route::post('sendmail', [UserController::class, 'sendmail'])->name('user.sendmail');
    
    
        //Export Routes
        Route::namespace('Admin')->group(function () {
            Route::get('export/users', [UserController::class, 'export'])->name('user.export');
            Route::get('export/participantdetails/{id}', [ProgramController::class, 'exportdetails'])->name('program.detailsexport');
            //Show email history
            Route::get('updateemails/{id}', [UserController::class, 'emailHistory'])->name('updateemails.show');
        });

        Route::resource('pop', PopController::class);
        Route::get('/temp-destroy/{id}', [PopController::class, 'tempDestroy'])->name('temp.destroy');
        //View proofofpayment
        Route::get('view/pop/{filename}', [PopController::class, 'getfile']);
    
        Route::resource('settings', SettingsController::class);
    
    
        Route::resource('tests', TestsController::class)->middleware(['programCheck']);
        Route::resource('mocks', MockController::class)->middleware(['programCheck']);
    
        // Route::get('/training/{p_id}', [HomeController::class, 'trainings'])->name('trainings.show')->middleware(['programCheck']);
        // Route::get('/my-wallet/{user_id}', [WalletController::class, 'participantWalletIndex'])->name('my.wallet');
        // Route::post('/top-up-account/{type?}', [PaymentController::class, 'accountTopUp'])->name('account.topup');
        // Route::get('/download-program-brochure/{p_id}', [HomeController::class, 'downloadProgramBrochure'])->name('download.program.brochure')->middleware(['programCheck']);
    
        Route::get('pretestresults', [MockController::class, 'pretest'])->name('pretest.select')->middleware(['programCheck']);
        Route::any('pretestresults/{id}', [MockController::class, 'getgrades'])->name('mocks.getgrades')->middleware(['programCheck']);
        Route::get('mockuser/{uid}/module/{modid}', [MockController::class, 'grade'])->middleware(['programCheck'])->name('mocks.add');
        
        Route::get('userresultscomments/{id}', [TestsController::class, 'userResultComments'])->middleware(['programCheck'])->name('tests.results.comment');
        // Route::get('balance-checkout', [HomeController::class, 'balanceCheckout'])->name('balance.checkout')->middleware(['programCheck']);
    
        Route::get('training.instructor', [ProfileController::class, 'showFacilitator'])->middleware(['programCheck'])->name('training.instructor');
    
        Route::get('mockresults', [MockController::class, 'mockresults'])->name('mocks.results');
        Route::resource('profiles', ProfileController::class);
        Route::resource('scoreSettings', ScoreSettingController::class);
    
        Route::get('selectfacilitator/{id}', [ProfileController::class, 'showFacilitator']);
        Route::POST('savefacilitator', [ProfileController::class, 'saveFacilitator'])->name('savefacilitator');
    
        Route::resource('complains', ComplainController::class);
    
        Route::get('crm-program-select/{p_id}', [ComplainController::class, 'getTrainingCrm'])->name('complain.program.select');
        Route::get('complainresolved/{complain}', [ComplainController::class, 'resolve'])->name('crm.resolved');
    
        
        Route::resource('teachers', TeacherController::class);
        Route::resource('companyuser', AdminCompanyUserController::class);
        
        Route::resource('coupon', CouponController::class);
        Route::get('teachers_students/{id}', [TeacherController::class, 'showStudents'])->name('teachers.students');
        Route::get('teachers_programs/{id}', [TeacherController::class, 'showPrograms'])->name('teachers.programs');
        Route::get('teachers_earnings/{id}', [TeacherController::class, 'showEarnings'])->name('teachers.earnings');
    
        Route::middleware(['programCheck'])->group(function () {
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
    
        // Programs Routes
    
        Route::middleware(['signed'])->group(function () {
            Route::resource('programs', ProgramController::class);
    
            Route::controller(ProgramController::class)->group(function () {
                Route::post('training-clone/{training}', 'cloneTraining')->name('training.clone');
                Route::get('complainshow/{crm}', 'showcrm')->name('crm.show');
                Route::get('restore/{id}', 'restore')->name('programs.restore');
                Route::get('complainhide/{crm}', 'hidecrm')->name('crm.hide');
                Route::get('close/{id}', 'closeRegistration')->name('registration.close');
                Route::get('open/{id}', 'openRegistration')->name('registration.open');
                Route::get('password-reset/{id}', 'passwordReset')->name('admin.password.reset');
                Route::get('earlybirdopen/{id}', 'openEarlyBird')->name('earlybird.open');
                Route::get('earlybirdclose/{id}', 'closeEarlyBird')->name('earlybird.close');
            });
    
            Route::controller(UserController::class)->group(function () {
                Route::get('participantsimport/{p_id}', 'importExport')->middleware(['programCheck'])->name('training.import');
                Route::post('import-training-participant', 'import')->middleware(['programCheck'])->name('users.import');
                Route::get('download-bulk-user-sample/{filename}', 'downloadBulkSample')->middleware(['programCheck'])->name('user-bulk-sample');
            });
        });
    
        Route::controller(ProgramController::class)->group(function () {
            Route::get('trashed-programs', 'trashed')->name('programs.trashed');
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
    
    
        // Modules Routes
        Route::resource('modules', ModuleController::class);
    
        Route::controller(ModuleController::class)->group(function () {
            Route::post('clonemodule', 'clone')->name('module.clone');
            Route::get('facilitatormodules/{p_id}', 'all')->name('facilitatormodules');
            Route::get('enablemodule/{id}', 'enablemodule')->name('modules.enable');
            Route::get('disablemodule/{id}', 'disablemodule')->name('modules.disable');
        });
    
        Route::resource('questions', QuestionController::class);
        // Route::resource('modules', ModuleController::class);
        Route::resource('programs', ProgramController::class);
    
        Route::middleware(['programCheck'])->group(function () {
            Route::resource('materials', MaterialController::class);
    
            Route::controller(MaterialController::class)->group(function () {
                Route::get('materialscreate/{p_id}', 'add')->name('creatematerials');
                // Route::get('facilitatormaterials/{p_id}', 'all')->name('facilitatormaterials');
                Route::post('cloneMaterial/{material_id}', 'clone')->name('material.clone');
                Route::get('studymaterials/{filename}/{p_id}', 'getfile')->name('getmaterial');
                Route::get('program-material/{training}', 'getTrainingMaterials')->name('material.program.select');
            });
        });
    
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
            
            Route::get('certificate-verification-logs', 'certificateVerificationLogs')->name('certificate.verification.logs');
            Route::get('truncate-verification-logs', 'truncateVerificationLogs')->name('truncate.verification.log');
        });
    
        //route for payments history
        Route::resource('payments', AdminPaymentController::class);
    
        Route::get('proof-history', [AdminPaymentController::class, 'proofOfPaymentHistory'])->name('proof.payment');
        Route::get('payment-history', [AdminPaymentController::class, 'paymentHistory'])->name('payments.history');
        Route::get('approve-wallet-transaction/{wallet_id}', [AdminPaymentController::class, 'approveWalletTransaction'])->name('approve.wallet.history');
        Route::get('delete-wallet-transaction/{wallet_id}', [AdminPaymentController::class, 'deleteWalletTransaction'])->name('delete.wallet.history');
        Route::get('printreceipt/{id}', [AdminPaymentController::class, 'printReceipt'])->name('payments.print');
    
        Route::resource('pictures', PictureController::class);
        Route::resource('details', DetailsController::class);
    
        Route::get('admin-remove-sub-program/{id}', [ProgramController::class, 'removeSubProgram']);
    });
});
