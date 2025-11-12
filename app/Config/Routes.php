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

// Role-based dashboards - ALL use Auth::dashboard
$routes->get('admin/dashboard', 'Auth::dashboard', ['filter' => 'roleauth']);
$routes->get('teacher/dashboard', 'Auth::dashboard', ['filter' => 'roleauth']); // ← CHANGED THIS
$routes->get('student/dashboard', 'Auth::dashboard', ['filter' => 'roleauth']);

// Admin specific routes
$routes->group('admin', ['filter' => 'roleauth'], function($routes){
    $routes->get('courses', 'Admin::courses');
    // Admin user management endpoints (add/update/delete) — verb-aware routes
    $routes->post('addUser', 'Admin::addUser');
    $routes->post('updateUser', 'Admin::updateUser');
    $routes->post('deleteUser', 'Admin::deleteUser');
});

// Announcements
$routes->get('announcement', 'Announcement::index');

// Materials routes
$routes->get('materials/course/(:num)', 'Materials::courseMaterials/$1');
$routes->get('materials/download/(:num)', 'Materials::download/$1');
$routes->get('materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('admin/course/(:num)/upload', 'Materials::upload/$1');

// Notification routes
$routes->get('notifications', 'Notifications::get');
$routes->post('notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
$routes->post('notifications/mark_all_read', 'Notifications::mark_all_as_read');

// Course enrollment route
$routes->post('course/enroll', 'Course::enroll');

