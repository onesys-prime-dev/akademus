<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('applynowpage', 'ApplyNow::index');
$routes->get('applicationform', 'ApplicationForm::index');
$routes->post('applicationform/submit', 'ApplicationForm::submit');