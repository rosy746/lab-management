<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Public Routes
$routes->get('/', 'Home::index');
$routes->get('books', 'Books::index');
$routes->get('books/(:num)', 'Books::detail/$1');
$routes->get('about', 'About::index');

// Auth Routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');

// Admin Routes
$routes->group('admin', function($routes) {
    $routes->get('/', 'Admin\Dashboard::index');
    $routes->get('books', 'Admin\Books::index');
    $routes->get('books/create', 'Admin\Books::create');
    $routes->post('books/store', 'Admin\Books::store');
    $routes->get('books/edit/(:num)', 'Admin\Books::edit/$1');
    $routes->post('books/update/(:num)', 'Admin\Books::update/$1');
    $routes->get('books/delete/(:num)', 'Admin\Books::delete/$1');
});