<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

// Auth routes
$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');

// Role-based dashboards
$routes->get('/auth/dashboard', 'Auth::dashboard');
$routes->get('/auth/dashboard', 'Auth::dashboard');
$routes->get('/auth/dashboard', 'Auth::dashboard');

// Fallback generic dashboard (if needed)
$routes->get('/dashboard', 'Auth::dashboard');
