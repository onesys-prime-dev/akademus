<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('applynowpage', 'ApplyNow::index');

// sign and sign up page after clicking the 'Apply Now' Button
$routes->get('sign_in', 'Auth::sign_in');
$routes->get('sign_up', 'Auth::sign_up');

// under sa Auth.php
$routes->post('sign_up/register', 'Auth::register');
$routes->post('sign_in/login', 'Auth::login');

$routes->get('logout', 'Auth::logout');

$routes->get('success', 'Auth::success');

// dashoard pages for student applicants
$routes->get('dashboard', 'Dashboard::index');

// applicant form steps routes
$routes->get('applicant-form', 'ApplicantForm::index');
$routes->get('applicant-form/step/(:num)', 'ApplicantForm::getStep/$1');
$routes->post('applicant-form/update/(:num)', 'ApplicantForm::updateForm/$1');
$routes->post('applicant-form/get-drafts', 'ApplicantForm::getSavedDraft');

// requirement steps routes
$routes->get('requirements', 'Requirements::index');
$routes->get('requirements/step/(:num)', 'Requirements::getStep/$1');

// get programs
$routes->get('get-programs', 'Program::getPrograms');

// Admin Auth
$routes->get('admin/login',  'ThemeController::adminLogin');
$routes->post('admin/login', 'ThemeController::adminLoginPost');
$routes->get('admin/logout', 'ThemeController::adminLogout');

// Theme Editor
$routes->get('admin/theme',       'ThemeController::index');
$routes->post('admin/theme/save', 'ThemeController::save');
$routes->get('admin/theme/json',  'ThemeController::getThemeJson');

// FAQ
$routes->get('faq', 'Faq::index');

// Calculator
$routes->get('tuition-fee-calculator', 'Dashboard::tuitionCalculator');

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