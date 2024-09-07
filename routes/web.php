<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestLoginController;
use App\Http\Controllers\Company\EmailVerifyController as CompanyEmailVerification;
use App\Http\Controllers\Student\EmailVerifyController as StudentEmailVerification;

// Import Livewire components
use App\Livewire\Common\{
    InterviewDetails,
    JobInterviewList,
    ListJobRegistrationInformationDetailsVrContent,
    ListJobRegistrationInformationDetails,
    ListJobRegistrationInformation,
    JobSeekerSearchView,
    JobSeekerSearch,
    SelectJobForScouting,
    ConfirmScouting,
    CreateJobListing
};
use App\Livewire\Student\Auth\{
    NewMemberRegistration as StudentNewMemberRegistration,
    UserRegistration as StudentUserRegistration,
    Login as StudentLogin
};
use App\Livewire\Company\Auth\{
    NewMemberRegistration as CompanyNewMemberRegistration,
    UserRegistration as CompanyUserRegistration,
    Login as CompanyLogin,
    ForgotPassword,
    ResetPassword
};
use App\Livewire\Company\{
    InterviewSchedule,
    InterviewScheduleManagement,
    EditProfile as CompanyEditProfile,
    EditCompanyInfo
};
use App\Livewire\Student\{
    InterviewApplicationTimeSelection,
    CandidateDetails,
    InterviewConfirmation,
    Dashboard
};
use App\Livewire\Company\Admin\ApproveRepresentatives;
use App\Livewire\Messages;
use Illuminate\Support\Facades\Mail;

Route::prefix('business-operator')->group(function () {
    Route::get('/login', \App\Livewire\BusinessOperator\Auth\Login::class)->name('business-operator.login');
    Route::get('/register', \App\Livewire\BusinessOperator\Auth\Register::class)->name('business-operator.register');
    Route::get('/forgot-password', \App\Livewire\BusinessOperator\Auth\ForgotPassword::class)->name('business-operator.password.request');
    Route::post('/forgot-password', [\App\Livewire\BusinessOperator\Auth\ForgotPassword::class, 'sendResetLink'])->name('business-operator.password.email');
    Route::get('/reset-password/{token}', \App\Livewire\BusinessOperator\Auth\ResetPassword::class)->name('business-operator.password.reset');
    Route::post('/reset-password', [\App\Livewire\BusinessOperator\Auth\ResetPassword::class, 'resetPassword'])->name('business-operator.password.update');
});

Route::get('/dashboard', Dashboard::class)->name('dashboard');

Route::middleware(['auth', 'check.user.type:BusinessOperator'])->group(function () {
    Route::get('/business-operator/approve', \App\Livewire\BusinessOperator\ApproveBusinessOperators::class)->name('business-operator.approve');
});

// Test Login Routes
Route::get('/test-login', [TestLoginController::class, 'index'])->name('test.login.index');
Route::get('/test-login/{id}', [TestLoginController::class, 'login'])->name('test.login');

//Testing send a Mail
// Route::get('/email/{mail}', function ($mail) {
//     Mail::to($mail)->send(new \App\Mail\PasswordResetSuccessfulMail());
//     return 'Test Email sent!';
// })
// ->name('mail.test');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::prefix('student')->group(function () {
        Route::get('/new-member-registration', StudentNewMemberRegistration::class)->name('student.new-member-registration');
        Route::get('/user-registration', StudentUserRegistration::class)->name('student.user-registration');
        Route::get('/login', StudentLogin::class)->name('student.login');
        Route::get('forgot-password', \App\Livewire\Student\Auth\ForgotPassword::class)->name('student.password.request');
        Route::post('forgot-password', [\App\Livewire\Student\Auth\ForgotPassword::class, 'sendResetLink'])->name('student.password.email');
        Route::get('reset-password/{token}', \App\Livewire\Student\Auth\ResetPassword::class)->name('student.password.reset');
        Route::post('reset-password', [\App\Livewire\Student\Auth\ResetPassword::class, 'resetPassword'])->name('student.password.update');
    });

    Route::prefix('company')->group(function () {
        Route::get('/new-member-registration', CompanyNewMemberRegistration::class)->name('company.new-member-registration');
        Route::get('/user-registration', CompanyUserRegistration::class)->name('company.user-registration');
        Route::get('/login', CompanyLogin::class)->name('company.login');
        Route::get('forgot-password', ForgotPassword::class)->name('company.password.request');
        Route::post('forgot-password', [ForgotPassword::class, 'sendResetLink'])->name('company.password.email');
        Route::get('reset-password/{token}', ResetPassword::class)->name('company.password.reset');
        Route::post('reset-password', [ResetPassword::class, 'resetPassword'])->name('company.password.update');
    });
});

// Email Verification Routes
Route::get('student/email/verify/{token}', StudentEmailVerification::class)->name('student.email.verify');
Route::get('company/email/verify/{token}', CompanyEmailVerification::class)->name('company.email.verify');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Message Routes
    Route::get('/messages', Messages::class)->name('messages');

    // Interview Confirmation Routes
    Route::middleware('check.user.type:Student,Candidate,CompanyRepresentative,CompanyAdmin')->group(function () {
        Route::get('/interview-confirmation/{interview}', InterviewConfirmation::class)->name('student.interview.confirmation');
    });

    // Student Routes
    Route::middleware('check.user.type:Student,Candidate')->group(function () {
        Route::get('/candidate-details/{vacancyId?}/{interviewId?}', CandidateDetails::class)->name('student.candidate-details');
        Route::get('/interview-application/{vacancyId?}/{interviewId?}', InterviewApplicationTimeSelection::class)->name('student.interview-application-time-selection');
    });

    // Company Routes
    Route::middleware('check.user.type:CompanyRepresentative,CompanyAdmin,Candidate,Student')->group(function () {
        Route::get('/company/interview-schedule', InterviewScheduleManagement::class)->name('company.interview-schedule');
        Route::get('/job-seeker/search', JobSeekerSearch::class)->name('job-seeker.search');
        Route::get('/job-interviews', JobInterviewList::class)->name('job-interviews');
        Route::get('/company/edit-profile', CompanyEditProfile::class)->name('company.edit-profile');
        Route::get('/company/edit-company-info', EditCompanyInfo::class)->name('company.edit-company-info');
        Route::get('/job-list/create', CreateJobListing::class)->name('job-list.create');

        Route::get('/vacancy/{vacancy}/interview-schedule', InterviewScheduleManagement::class)->name('company.vacancy.interview-schedule');
        Route::put('/job-details/{id}', [ListJobRegistrationInformationDetails::class, 'save'])->name('job-details.update');

        Route::get('/job-seeker/view/{id}', JobSeekerSearchView::class)->name('job-seeker.view');
        Route::get('/interview/{interview}', InterviewDetails::class)->name('interview.details');
        // Route::get('/interview/{interview}', InterviewDetails::class)->name('candidate.interview.details');

    });

    // Company Admin Routes
    Route::middleware('check.user.type:CompanyAdmin')->group(function () {
        Route::get('/company/approve-representatives', ApproveRepresentatives::class)->name('company.approve-representatives');
    });

    // Shared Routes
    Route::middleware('check.user.type:CompanyRepresentative,CompanyAdmin,Student,Candidate')->group(function () {
        Route::get('/job-list/search', ListJobRegistrationInformation::class)->name('job-list.search');
        Route::get('/job-details/{id}', ListJobRegistrationInformationDetails::class)->name('job-details');
        Route::get('/job/{vacancyId}/vr-content/{contentType}', ListJobRegistrationInformationDetailsVrContent::class)->name('job.vr-content');
        Route::put('/job/{vacancyId}/vr-content/{contentType}', [ListJobRegistrationInformationDetailsVrContent::class, 'save'])->name('job.vr-content.update');
    });
});

// Scouting Routes (These might need authentication middleware)
Route::middleware('check.user.type:CompanyRepresentative,CompanyAdmin,Student,Candidate')->group(function () {
    Route::get('/select-job/{candidateId}', SelectJobForScouting::class)->name('job-seeker.select-job');
    Route::get('/confirm-scouting/{candidateId}/{jobId}', ConfirmScouting::class)->name('job-seeker.confirm-scouting');
});

// Dynamic Routes (at the end)
Route::get('/{viewType?}', function ($viewType = null) {
    Session::put('view_type', $viewType === 'company' ? 'company' : 'student');
    return view('home.index', ['viewType' => Session::get('view_type')]);
})->name('home');

require __DIR__ . '/auth.php';
