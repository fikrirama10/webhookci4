<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('webhook/handle', 'Webhook::handle');
$routes->post('auth/login', 'Auth::loginWebhook');
$routes->post('auth/logout', 'Auth::logoutWebhook');
$routes->post('auth/register', 'Auth::register');