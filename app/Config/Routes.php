<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'LandingPage\HomeController::index');
$routes->get('applynowpage', 'ApplyNow::index');

// -------------------------- LANDING PAGE ROUTES -------------------------- //
$routes->get('/homepage', 'LandingPage\HomeController::index');     // Home           
$routes->get('/about-us', 'LandingPage\AboutUsController::index');  // About Us
$routes->get('/features', 'LandingPage\FeaturesController::index'); // Features
$routes->get('/contact', 'LandingPage\ContactController::index');   // Contact

$routes->get('/systems/akademus', 'LandingPage\SystemsController::akademus');
$routes->get('/systems/mentora', 'LandingPage\SystemsController::mentora');
$routes->get('/systems/learnix', 'LandingPage\SystemsController::learnix');
$routes->get('/systems/sis', 'LandingPage\SystemsController::sis');
$routes->get('/systems/attendance', 'LandingPage\SystemsController::attendance');
$routes->get('/systems/otis', 'LandingPage\SystemsController::otis');
// ------------------------------------------------------------------------- //

// sign and sign up page after clicking the 'Apply Now' Button
$routes->get('sign-in', 'Auth\AuthController::sign_in');
$routes->get('sign-up', 'Auth\AuthController::sign_up');

// under sa Auth.php
$routes->post('sign-up/register', 'Auth\AuthController::register');
$routes->post('sign-in/login', 'Auth\AuthController::login');

$routes->get('logout',                  'Auth\AuthController::logout');
$routes->post('change-password',         'Auth\AuthController::changePassword');
$routes->post('forgot-password',         'Auth\AuthController::forgotPassword');
$routes->get('reset-password/(:segment)', 'Auth\AuthController::showResetForm/$1');
$routes->post('reset-password',          'Auth\AuthController::processReset');

$routes->get('success', 'Auth\AuthController::success');

// dashoard pages for student applicants
$routes->get('dashboard', 'Applicant\DashboardController::index');

// applicant form steps routes
$routes->get('applicant-form', 'Applicant\ApplicantFormController::index');
$routes->get('applicant-form/step/(:num)', 'Applicant\ApplicantFormController::getStep/$1');
$routes->post('applicant-form/update/(:num)', 'Applicant\ApplicantFormController::updateForm/$1');
$routes->post('applicant-form/get-drafts', 'Applicant\ApplicantFormController::getSavedDraft');

// tracking progress
$routes->get('tracking-progress', 'Applicant\TrackingProgressController::index');

// Applicant Exam Schedule (only accessible when exam_enabled = 1)
$routes->get('exam-schedule',        'Applicant\ExamScheduleController::index');
$routes->post('exam-schedule/book',   'Applicant\ExamScheduleController::book');
$routes->post('exam-schedule/cancel', 'Applicant\ExamScheduleController::cancel');

// requirement steps routes
$routes->get('requirements', 'Applicant\RequirementsController::index');
$routes->get('requirements/step/(:num)', 'Applicant\RequirementsController::getStep/$1');
$routes->post('requirements/update/(:num)', 'Applicant\RequirementsController::updateDocument/$1');
$routes->get('requirements/document/(:num)', 'Applicant\RequirementsController::viewDocument/$1');
$routes->post('requirements/submit', 'Applicant\RequirementsController::submitApplication');

// get programs
$routes->get('get-programs', 'Program\ProgramController::getPrograms');

// Admin Auth
$routes->get('admin/login',  'ThemeController::adminLogin');
$routes->post('admin/login', 'ThemeController::adminLoginPost');
$routes->get('admin/logout', 'ThemeController::adminLogout');

// Admin User Management
$routes->get('admin/users',                          'Admin\UsersController::index');
$routes->post('admin/users/store',                    'Admin\UsersController::store');
$routes->post('admin/users/(:num)/update',            'Admin\UsersController::update/$1');
$routes->post('admin/users/(:num)/reset-password',    'Admin\UsersController::resetPassword/$1');
$routes->post('admin/users/(:num)/delete',            'Admin\UsersController::delete/$1');

// Theme Editor
$routes->get('admin/theme',            'ThemeController::index');
$routes->post('admin/theme/save',      'ThemeController::save');
$routes->get('admin/theme/json',       'ThemeController::getThemeJson');
$routes->get('admin/theme/preview',    'ThemeController::preview');
$routes->post('admin/theme/upload-logo', 'ThemeController::uploadLogo');


// -------------------------------- PAYMENT FEES ------------------------------ //
// Applicant — submit payments
$routes->get('admission-processing-fee',         'Payment\AdmissionProcessingFeeController::admissionProcessingFee');
$routes->post('admission-processing-fee/submit', 'Payment\AdmissionProcessingFeeController::store');
$routes->get('admission-processing-fee/proof/(:num)', 'Payment\AdmissionProcessingFeeController::proofFile/$1');

$routes->get('reservation-fee',         'Payment\ReservationFeeController::index');
$routes->post('reservation-fee/submit', 'Payment\ReservationFeeController::store');

// SMART PAY Webhook
$routes->post(
    'smartpay/webhook',
    'Payment\SmartpayWebhookController::index'
);

// Admin — fee amount settings only (no payment review)
$routes->get('admin/payment-fees',                        'Admin\PaymentFeesController::fees');
$routes->post('admin/payment-fees/update/(:num)',          'Admin\PaymentFeesController::updateFee/$1');
$routes->post('admin/payment-methods/add',                 'Admin\PaymentFeesController::addMethod');
$routes->post('admin/payment-methods/update/(:num)',       'Admin\PaymentFeesController::updateMethod/$1');
$routes->post('admin/payment-methods/toggle/(:num)',       'Admin\PaymentFeesController::toggleMethod/$1');

// Admission Officer — payment review (approve/reject + proof file)
$routes->post('admission-officer/payments/(:num)/approve', 'AdmissionOfficer\PaymentController::approve/$1');
$routes->post('admission-officer/payments/(:num)/reject',  'AdmissionOfficer\PaymentController::reject/$1');
$routes->get('admission-officer/payments/(:num)/proof',    'AdmissionOfficer\PaymentController::proofFile/$1');
// ----------------------------------------------------------------------------- //

// Admin System Settings
$routes->get('admin/settings',      'Admin\SettingsController::index');
$routes->post('admin/settings/save', 'Admin\SettingsController::save');


// FAQ
$routes->get('faq', 'Applicant\FaqController::index');

// Calculator
$routes->get('tuition-fee-calculator', 'Applicant\TuitionFeeCalculatorController::tuitionCalculator');

// Admin Form Field Manager
$routes->get('admin/form-fields',                     'Admin\FormFields::index');
$routes->get('admin/form-fields/page-data',           'Admin\FormFields::pageData');
$routes->post('admin/form-fields/save',                'Admin\FormFields::save');
$routes->post('admin/form-fields/reset',               'Admin\FormFields::reset');

// Page management (reorder must come before (:num) routes)
$routes->post('admin/form-fields/pages',               'Admin\FormFields::pageCreate');
$routes->post('admin/form-fields/pages/reorder',       'Admin\FormFields::pageReorder');
$routes->post('admin/form-fields/pages/(:num)/toggle', 'Admin\FormFields::pageToggle/$1');
$routes->post('admin/form-fields/pages/(:num)/rename', 'Admin\FormFields::pageRename/$1');
$routes->post('admin/form-fields/pages/(:num)/delete', 'Admin\FormFields::deletePage/$1');

// Admin Form Presets Manager
$routes->get('admin/presets',                    'Admin\PresetsController::index');
$routes->get('admin/presets/create',             'Admin\PresetsController::create');
$routes->post('admin/presets/store',             'Admin\PresetsController::store');
$routes->get('admin/presets/(:num)/edit',        'Admin\PresetsController::edit/$1');
$routes->post('admin/presets/(:num)/update',     'Admin\PresetsController::update/$1');
$routes->get('admin/presets/(:num)/deactivate',  'Admin\PresetsController::deactivate/$1');
$routes->get('admin/presets/(:num)/delete',      'Admin\PresetsController::delete/$1');
$routes->get('admin/presets/(:num)/json',        'Admin\PresetsController::getPresetJson/$1');

// Admin Requirement Categories Manager
$routes->get('admin/requirement-categories',                        'Admin\RequirementCategoriesController::index');
$routes->get('admin/requirement-categories/create',                 'Admin\RequirementCategoriesController::create');
$routes->post('admin/requirement-categories/store',                  'Admin\RequirementCategoriesController::store');
$routes->get('admin/requirement-categories/(:num)/edit',            'Admin\RequirementCategoriesController::edit/$1');
$routes->post('admin/requirement-categories/(:num)/update',          'Admin\RequirementCategoriesController::update/$1');
$routes->get('admin/requirement-categories/(:num)/delete',          'Admin\RequirementCategoriesController::delete/$1');

// Admin Program Management (CRUD)
$routes->get('admin/programs',                  'Admin\CoursesController::index');
$routes->get('admin/programs/create',           'Admin\CoursesController::create');
$routes->post('admin/programs/create',           'Admin\CoursesController::create');
$routes->get('admin/programs/(:num)/edit',      'Admin\CoursesController::edit/$1');
$routes->post('admin/programs/(:num)/edit',      'Admin\CoursesController::edit/$1');
$routes->get('admin/programs/(:num)/delete',    'Admin\CoursesController::delete/$1');

// Admin Program-Preset Management
$routes->get('admin/programs/presets',           'Admin\ProgramPresetsController::index');
$routes->post('admin/programs/assign-preset',    'Admin\ProgramPresetsController::assignPreset');

// Admin Payment Fee Management
// $routes->get('admin/payment-fees', 'Admin\PaymentFeesController::index');
// $routes->post('admin/payment-fees/update', 'Admin\PaymentFeesController::update');

// API Endpoints
$routes->get('api/presets/program/(:num)',       'Admin\PresetsController::getFormPagesForApplicant/$1');

// Admission Officer Auth
$routes->get('admission-officer/login',  'AdmissionOfficer\AuthController::login');
$routes->post('admission-officer/login',  'AdmissionOfficer\AuthController::loginPost');
$routes->get('admission-officer/logout', 'AdmissionOfficer\AuthController::logout');

// Admission Officer Dashboard
$routes->get('admission-officer/dashboard',             'AdmissionOfficer\DashboardController::index');
$routes->get('admission-officer/dashboard/search',      'AdmissionOfficer\DashboardController::search');
$routes->get('admission-officer/dashboard/data',        'AdmissionOfficer\DashboardController::data');
$routes->get('admission-officer/dashboard/calendar-data', 'AdmissionOfficer\DashboardController::calendarData');

// Admission Officer Applicants
$routes->get('admission-officer/applicants/(:num)',                          'AdmissionOfficer\ApplicantsController::index/$1');
$routes->get('admission-officer/applicants/fetch-content',                   'AdmissionOfficer\ApplicantsController::fetchContent');
$routes->get('admission-officer/applicants/document/(:num)/(:num)',          'AdmissionOfficer\ApplicantsController::viewDocument/$1/$2');
$routes->post('admission-officer/applicants/document/(:num)/decide',        'AdmissionOfficer\ApplicantsController::decideDocument/$1');
$routes->get('admission-officer/applicants',                     'AdmissionOfficer\ApplicantsController::index');
$routes->get('admission-officer/applicants/fetch-applicants',    'AdmissionOfficer\ApplicantsController::fetchApplicants');
$routes->post('admission-officer/applicants/bulk-update',        'AdmissionOfficer\ApplicantsController::bulkUpdate');
$routes->get('admission-officer/detailed-applicant/(:num)',         'AdmissionOfficer\ApplicantsController::profile/$1');
$routes->get('admission-officer/detailed-applicant/fetch-content',  'AdmissionOfficer\ApplicantsController::fetchContent');
$routes->post('admission-officer/applicants/(:num)/decide',         'AdmissionOfficer\ApplicantsController::decide/$1');

// Admission Officer Exam Schedule
$routes->get('admission-officer/exam-schedule',                         'AdmissionOfficer\ExamScheduleController::index');
$routes->get('admission-officer/exam-schedule/content',                 'AdmissionOfficer\ExamScheduleController::content');
$routes->get('admission-officer/exam-schedule/show/(:num)',             'AdmissionOfficer\ExamScheduleController::show/$1');
$routes->post('admission-officer/exam-schedule/create',                 'AdmissionOfficer\ExamScheduleController::create');
$routes->post('admission-officer/exam-schedule/update/(:num)',          'AdmissionOfficer\ExamScheduleController::update/$1');
$routes->post('admission-officer/exam-schedule/delete/(:num)',          'AdmissionOfficer\ExamScheduleController::delete/$1');
$routes->post('admission-officer/exam-schedule/bookings/check-in/(:num)', 'AdmissionOfficer\ExamScheduleController::checkIn/$1');
$routes->post('admission-officer/exam-schedule/bookings/cancel/(:num)', 'AdmissionOfficer\ExamScheduleController::cancelBooking/$1');
$routes->post(
    'admission-officer/exam-bookings/(:num)/result',
    'AdmissionOfficer\ApplicantsController::saveExamResult/$1'
);

// Admission Officer Follow-Up Messages
$routes->get('admission-officer/follow-up',                        'AdmissionOfficer\FollowUpController::index');
$routes->get('admission-officer/follow-up/(:num)',                  'AdmissionOfficer\FollowUpController::conversation/$1');
$routes->post('admission-officer/follow-up/send',                    'AdmissionOfficer\FollowUpController::send');
$routes->get('admission-officer/follow-up/messages',                'AdmissionOfficer\FollowUpController::fetchMessages');
$routes->get('admission-officer/follow-up/conversations',           'AdmissionOfficer\FollowUpController::fetchConversations');
$routes->get('admission-officer/follow-up/unread-count',            'AdmissionOfficer\FollowUpController::unreadCount');

// Applicant Follow-Up Messages
$routes->get('follow-up-messages',             'Applicant\FollowUpController::index');
$routes->post('follow-up-messages/send',        'Applicant\FollowUpController::send');
$routes->get('follow-up-messages/fetch',       'Applicant\FollowUpController::fetchMessages');
$routes->get('follow-up-messages/unread-count', 'Applicant\FollowUpController::unreadCount');
// Tracking Progress
$routes->get('tracking-progress', 'Applicant\TrackingProgressController::index');

// (duplicate route removed — handled by Payment\AdmissionProcessingFeeController above)

// API Route for Groq AI Assistant
$routes->post('api/groq/chat', 'API\Groq::chat');

// Smart Pay
$routes->post('/payment/admission-fee', 'Payment\SmartPayController::getAdmissionFeePayment');
$routes->post('/payment/reservation-fee', 'Payment\SmartPayController::getReservationFeePayment');
