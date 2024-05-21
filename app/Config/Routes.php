<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('documents/', 'DocumentController::index');
$routes->post('documents/save/','DocumentController::save');
$routes->get('documents/show/(:num)', 'DocumentController::showPDF/$1');
