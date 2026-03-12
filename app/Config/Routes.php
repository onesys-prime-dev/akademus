<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('applynowpage', 'ApplyNow::index');

// sign and sign up page after clicking the 'Apply Now' Button
$routes->get('sign-in', 'Auth\AuthController::sign_in');
$routes->get('sign-up', 'Auth\AuthController::sign_up');

// under sa Auth.php
$routes->post('sign-up/register', 'Auth\AuthController::register');
$routes->post('sign-in/login', 'Auth\AuthController::login');

$routes->get('logout', 'Auth\AuthController::logout');

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

// get programs
$routes->get('get-programs', 'Program\ProgramController::getPrograms');

// Admin Auth
$routes->get('admin/login',  'ThemeController::adminLogin');
$routes->post('admin/login', 'ThemeController::adminLoginPost');
$routes->get('admin/logout', 'ThemeController::adminLogout');

// Theme Editor
$routes->get('admin/theme',       'ThemeController::index');
$routes->post('admin/theme/save', 'ThemeController::save');
$routes->get('admin/theme/json',  'ThemeController::getThemeJson');  // used by portal views


//faq
$routes->get('faq', 'Applicant\FaqController::index');

//Calculator
$routes->get('tuition-fee-calculator', 'Applicant\TuitionFeeCalculatorController::tuitionCalculator');
