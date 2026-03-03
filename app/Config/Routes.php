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

// requirement steps routes
$routes->get('requirements', 'Requirements::index');
$routes->get('requirements/step/(:num)', 'Requirements::getStep/$1');
