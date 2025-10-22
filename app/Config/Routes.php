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

// Role-based dashboards (grouped and protected)
$routes->group('admin', ['filter' => 'roleauth'], function($routes){
    $routes->get('dashboard', 'Admin::dashboard'); // /admin/dashboard
    $routes->get('courses', 'Admin::courses');    // /admin/courses
    // add other admin routes here (e.g. $routes->get('users', 'Admin::users'); )
});

$routes->group('teacher', ['filter' => 'roleauth'], function($routes){
    $routes->get('dashboard', 'Teacher::dashboard'); // /teacher/dashboard
    // add other teacher routes here
});

// student/dashboard can be left ungrouped or grouped similarly if needed
$routes->get('student/dashboard', 'Auth::dashboard');

// announcements (public / student-accessible)
$routes->get('announcement', 'Announcement::index');
$routes->get('announcements', 'Announcement::index'); // pick one name and use it consistently

// Materials routes
$routes->get('admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('materials/download/(:num)', 'Materials::download/$1');
// AJAX: list materials for a course (used by dashboard modal)
$routes->get('materials/course/(:num)', 'Materials::courseMaterials/$1');
$routes->get('materials/course/(:num)', 'Materials::courseMaterials/$1');

// Log the auth session after login
$routes->post('/login', function(){
    log_message('debug', 'Auth session after login: ' . json_encode(session()->get()));
});

