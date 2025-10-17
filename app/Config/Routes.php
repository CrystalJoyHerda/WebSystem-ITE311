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

// Log the auth session after login
$routes->post('/login', function(){
    log_message('debug', 'Auth session after login: ' . json_encode(session()->get()));
});
