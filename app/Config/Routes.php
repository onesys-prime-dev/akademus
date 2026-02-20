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
$routes->get('dashboard', 'Dashboard::index');