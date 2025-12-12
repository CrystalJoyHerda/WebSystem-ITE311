<?php

// Reject malformed or malicious request URIs early to prevent directory
// traversal or accidental routing outside the project (e.g., to XAMPP dashboard).
if (PHP_SAPI !== 'cli') {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    // If request contains ".." or a double slash path segment, return 400
    if (strpos($requestUri, '..') !== false || strpos($requestUri, '//') !== false) {
        header('HTTP/1.1 400 Bad Request');
        echo '400 Bad Request';
        exit;
    }
}

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
$routes->get('admin/dashboard', 'Auth::dashboard');
$routes->get('teacher/dashboard', 'Teacher::dashboard');
$routes->get('student/dashboard', 'Auth::dashboard' );

// Teacher specific routes
$routes->group('teacher', ['filter' => 'roleauth'], function($routes){
    $routes->get('course/(:num)/subjects', 'Teacher::getCourseSubjects/$1');
    $routes->get('course/(:num)/students', 'Teacher::getCourseStudents/$1');
    $routes->get('course/(:num)/quizzes', 'Teacher::getCourseQuizzes/$1');
    $routes->get('course/(:num)/assignments', 'Teacher::getCourseAssignments/$1');
    // Enrollment approval routes
    $routes->post('enroll/approve', 'Teacher::approveEnrollment');
    $routes->post('enroll/reject', 'Teacher::rejectEnrollment');
});

// Admin specific routes
$routes->group('admin', ['filter' => 'roleauth'], function($routes){
    $routes->get('courses', 'Admin::courses');
    // Admin user management endpoints (add/update/delete) — verb-aware routes
    $routes->post('addUser', 'Admin::addUser');
    $routes->post('updateUser', 'Admin::updateUser');
    $routes->post('deleteUser', 'Admin::deleteUser');
    // User search endpoint (supports both GET and POST for AJAX)
    $routes->get('users/search', 'Admin::searchUsers');
    $routes->post('users/search', 'Admin::searchUsers');
    // Manage inactive users
    $routes->get('aboutUsers', 'Admin::aboutUsers');
    $routes->post('activateUser', 'Admin::activateUser');
    // Course management routes
    $routes->post('courses/create', 'Admin::createCourse');
    $routes->post('courses/update', 'Admin::updateCourse');
    $routes->post('courses/assign', 'Admin::assignTeacher');
    $routes->get('courses/list', 'Admin::getCourses');
    // Course search endpoint (supports both GET and POST for AJAX)
    $routes->get('courses/search', 'Admin::searchCourses');
    $routes->post('courses/search', 'Admin::searchCourses');
    $routes->get('teachers/list', 'Admin::getTeachers');
    // Student enrollment routes
    $routes->post('enroll/auto', 'Admin::autoEnroll');
    $routes->post('enroll/assign', 'Admin::assignCourse');
    $routes->get('students/list', 'Admin::getStudents');
});

// Announcements
$routes->get('announcement', 'Announcement::index');

// Course enrollment request (student requests enrollment in assigned course)
$routes->post('course/request-enrollment', 'Course::requestEnrollment');

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

// Assignment routes
$routes->get('assignments/create/(:num)', 'Assignments::create/$1');
$routes->post('assignments/create/(:num)', 'Assignments::create/$1');
$routes->get('assignments/view/(:num)', 'Assignments::view/$1');
$routes->post('assignments/delete/(:num)', 'Assignments::delete/$1');
$routes->get('assignments/download/(:num)', 'Assignments::download/$1');

// Submission routes
$routes->post('submissions/submit/(:num)', 'Submissions::submit/$1');
$routes->get('submissions/download/(:num)', 'Submissions::downloadSubmission/$1');
$routes->post('submissions/grade/(:num)', 'Submissions::grade/$1');
$routes->get('submissions/view/(:num)', 'Submissions::viewSubmissions/$1');
$routes->get('submissions/checkStatus/(:num)', 'Submissions::checkStatus/$1');

// Course enrollment route
$routes->post('course/enroll', 'Course::enroll');

