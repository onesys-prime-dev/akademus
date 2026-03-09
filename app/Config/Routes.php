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

// get programs
$routes->get('get-programs', 'Program\ProgramController::getPrograms');
