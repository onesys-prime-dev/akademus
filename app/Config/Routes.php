<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('applynowpage', 'ApplyNow::index');



//About us

$routes->get('about', 'AboutController::index');

// sign and sign up page after clicking the 'Apply Now' Button
$routes->get('sign-in', 'Auth\AuthController::sign_in');
$routes->get('sign-up', 'Auth\AuthController::sign_up');

// under sa Auth.php
$routes->post('sign-up/register', 'Auth\AuthController::register');
$routes->post('sign-in/login', 'Auth\AuthController::login');

$routes->get ('logout',                  'Auth\AuthController::logout');
$routes->post('change-password',         'Auth\AuthController::changePassword');
$routes->post('forgot-password',         'Auth\AuthController::forgotPassword');
$routes->get ('reset-password/(:segment)','Auth\AuthController::showResetForm/$1');
$routes->post('reset-password',          'Auth\AuthController::processReset');

$routes->get('success', 'Auth\AuthController::success');

// dashoard pages for student applicants
$routes->get('dashboard', 'Applicant\DashboardController::index');

// applicant form steps routes
$routes->get('applicant-form', 'Applicant\ApplicantFormController::index');
$routes->get('applicant-form/step/(:num)', 'Applicant\ApplicantFormController::getStep/$1');
$routes->post('applicant-form/update/(:num)', 'Applicant\ApplicantFormController::updateForm/$1');
$routes->post('applicant-form/get-drafts', 'Applicant\ApplicantFormController::getSavedDraft');

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

// Theme Editor
$routes->get('admin/theme',       'ThemeController::index');
$routes->post('admin/theme/save', 'ThemeController::save');
$routes->get('admin/theme/json',  'ThemeController::getThemeJson');

// FAQ
$routes->get('faq', 'Applicant\FaqController::index');

// Calculator
$routes->get('tuition-fee-calculator', 'Applicant\TuitionFeeCalculatorController::tuitionCalculator');

// Admin Form Field Manager
$routes->get ('admin/form-fields',                     'Admin\FormFields::index');
$routes->get ('admin/form-fields/page-data',           'Admin\FormFields::pageData');
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
$routes->get ('admin/requirement-categories',                        'Admin\RequirementCategoriesController::index');
$routes->get ('admin/requirement-categories/create',                 'Admin\RequirementCategoriesController::create');
$routes->post('admin/requirement-categories/store',                  'Admin\RequirementCategoriesController::store');
$routes->get ('admin/requirement-categories/(:num)/edit',            'Admin\RequirementCategoriesController::edit/$1');
$routes->post('admin/requirement-categories/(:num)/update',          'Admin\RequirementCategoriesController::update/$1');
$routes->get ('admin/requirement-categories/(:num)/delete',          'Admin\RequirementCategoriesController::delete/$1');

// Admin Program-Preset Management
$routes->get('admin/programs/presets',           'Admin\ProgramPresetsController::index');
$routes->post('admin/programs/assign-preset',    'Admin\ProgramPresetsController::assignPreset');

// API Endpoints
$routes->get('api/presets/program/(:num)',       'Admin\PresetsController::getFormPagesForApplicant/$1');

// Admission Officer Dashboard
$routes->get('admission-officer/dashboard',                     'AdmissionOfficer\DashboardController::index');
$routes->get('admission-officer/dashboard/fetch-applicants',    'AdmissionOfficer\DashboardController::fetchApplicants');
$routes->post('admission-officer/dashboard/bulk-update',        'AdmissionOfficer\DashboardController::bulkUpdate');

// Admission Officer Applicants
$routes->get('admission-officer/applicants/(:num)',         'AdmissionOfficer\ApplicantsController::index/$1');
$routes->get('admission-officer/applicants/fetch-content',  'AdmissionOfficer\ApplicantsController::fetchContent');

// Admission Officer Follow-Up Messages
$routes->get ('admission-officer/follow-up',                        'AdmissionOfficer\FollowUpController::index');
$routes->get ('admission-officer/follow-up/(:num)',                  'AdmissionOfficer\FollowUpController::conversation/$1');
$routes->post('admission-officer/follow-up/send',                    'AdmissionOfficer\FollowUpController::send');
$routes->get ('admission-officer/follow-up/messages',                'AdmissionOfficer\FollowUpController::fetchMessages');
$routes->get ('admission-officer/follow-up/conversations',           'AdmissionOfficer\FollowUpController::fetchConversations');
$routes->get ('admission-officer/follow-up/unread-count',            'AdmissionOfficer\FollowUpController::unreadCount');

// Applicant Follow-Up Messages
$routes->get ('follow-up-messages',             'Applicant\FollowUpController::index');
$routes->post('follow-up-messages/send',        'Applicant\FollowUpController::send');
$routes->get ('follow-up-messages/fetch',       'Applicant\FollowUpController::fetchMessages');
$routes->get ('follow-up-messages/unread-count','Applicant\FollowUpController::unreadCount');