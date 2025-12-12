<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - LMS</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        .course-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .course-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            border-radius: 10px 10px 0 0;
            min-height: 90px;
            display: flex;
            align-items: center;
        }
        .course-header h5 {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .course-header h6 {
            font-size: 0.75rem;
        }
        .course-card .card-body {
            min-height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 12px;
        }
        .course-card .card-body .small {
            font-size: 0.7rem;
        }
        .course-card .badge-custom {
            font-size: 0.7rem;
            padding: 4px 8px;
        }
        .subjects-container {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .subjects-container.show {
            display: block;
        }
        .subject-item {
            background: white;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 6px;
            border-left: 4px solid #667eea;
            transition: all 0.2s ease;
        }
        .subject-item:hover {
            border-left-color: #764ba2;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .badge-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .spinner-container {
            text-align: center;
            padding: 20px;
        }
        .header-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .expand-btn {
            transition: transform 0.3s ease;
        }
        .expand-btn.rotated {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1"><i class="fas fa-chalkboard-teacher text-primary me-2"></i>Teacher Dashboard</h2>
                    <p class="text-muted mb-0">Welcome back, <?= esc($userName ?? 'Teacher') ?>!</p>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <a href="<?= base_url('announcement') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-bullhorn me-1"></i>Announcements
                    </a>
                    
                    <!-- Notification Bell -->
                    <div class="dropdown">
                        <button class="btn btn-outline-info position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none; font-size: 0.7rem;">
                                0
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
                            <li class="dropdown-header d-flex justify-content-between align-items-center">
                                <span><strong>Notifications</strong></span>
                                <button class="btn btn-sm btn-link text-decoration-none" id="markAllRead" style="font-size: 0.85rem;">Mark all as read</button>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <div id="notificationList">
                                <li class="dropdown-item-text text-center text-muted py-3">
                                    <i class="fas fa-spinner fa-spin"></i> Loading...
                                </li>
                            </div>
                        </ul>
                    </div>
                    
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-1"></i><?= esc($userName ?? 'Teacher') ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Section -->
        <div class="row">
            <div class="col-12">
                <h4 class="text-white mb-3"><i class="fas fa-book me-2"></i>My Courses</h4>
            </div>
        </div>

        <?php if (!empty($courses)): ?>
            <div class="row" id="coursesAccordion">
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card course-card" data-course-id="<?= esc($course['id']) ?>">
                            <div class="course-header">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1"><?= esc($course['subject_code']) ?></h5>
                                        <h6 class="fw-normal"><?= esc($course['subject_name']) ?></h6>
                                    </div>
                                    <button class="btn btn-sm btn-light expand-btn" 
                                            type="button"
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#actions-<?= esc($course['id']) ?>" 
                                            aria-expanded="false" 
                                            aria-controls="actions-<?= esc($course['id']) ?>">
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <?php if (!empty($course['description'])): ?>
                                    <p class="text-muted small mb-3"><?= esc($course['description']) ?></p>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge badge-custom">
                                            <i class="fas fa-users me-1"></i>
                                            <?= esc($course['enrolled_count'] ?? 0) ?> Students
                                        </span>
                                    </div>
                                    <?php if (!empty($course['semester'])): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i><?= esc($course['semester']) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Action Buttons Collapsible Panel -->
                                <div class="collapse mt-3" id="actions-<?= esc($course['id']) ?>" data-bs-parent="#coursesAccordion">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="openStudentsModal(<?= esc($course['id']) ?>, '<?= esc($course['subject_code']) ?>')">
                                            <i class="fas fa-user-graduate me-1"></i>Students
                                        </button>
                                        <button class="btn btn-outline-success btn-sm" onclick="openQuizzesModal(<?= esc($course['id']) ?>, '<?= esc($course['subject_code']) ?>')">
                                            <i class="fas fa-question-circle me-1"></i>Quizzes
                                        </button>
                                        <button class="btn btn-outline-warning btn-sm" onclick="openAssignmentsModal(<?= esc($course['id']) ?>, '<?= esc($course['subject_code']) ?>')">
                                            <i class="fas fa-tasks me-1"></i>Assignments
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Subjects Container (loaded via AJAX) -->
                                <div class="subjects-container" id="subjects-<?= esc($course['id']) ?>">
                                    <div class="spinner-container">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="small text-muted mt-2">Loading subjects...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No courses assigned yet. Please contact the administrator.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Notification functionality
        $(function(){
            // Load notifications
            loadNotifications();
            
            // Refresh notifications every 30 seconds
            setInterval(loadNotifications, 30000);
            
            // Mark all as read
            $('#markAllRead').on('click', function(e) {
                e.preventDefault();
                $.ajax({
                    url: '<?= base_url('notifications/markAllRead') ?>',
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            loadNotifications();
                        }
                    }
                });
            });
            
            // Mark single notification as read
            $(document).on('click', '.notification-item', function() {
                const notificationId = $(this).data('id');
                if (notificationId && $(this).hasClass('unread')) {
                    $.ajax({
                        url: '<?= base_url('notifications/markAsRead') ?>',
                        method: 'POST',
                        data: { id: notificationId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                loadNotifications();
                            }
                        }
                    });
                }
            });
        });

        function loadNotifications() {
            $.ajax({
                url: '<?= base_url('notifications') ?>',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update badge count
                        const unreadCount = response.unreadCount;
                        const badge = $('#notificationBadge');
                        
                        if (unreadCount > 0) {
                            badge.text(unreadCount > 99 ? '99+' : unreadCount);
                            badge.show();
                        } else {
                            badge.hide();
                        }
                        
                        // Update notification list
                        const notificationList = $('#notificationList');
                        if (response.notifications && response.notifications.length > 0) {
                            let html = '';
                            response.notifications.forEach(function(notif) {
                                const isUnread = notif.is_read == 0 || notif.is_read === '0';
                                const unreadClass = isUnread ? 'unread bg-light' : '';
                                const icon = notif.type === 'announcement' ? 'fa-bullhorn' : 'fa-info-circle';
                                html += `
                                    <li>
                                        <a class="dropdown-item notification-item ${unreadClass}" href="#" data-id="${notif.id}" style="white-space: normal; padding: 10px 15px;">
                                            <div class="d-flex">
                                                <i class="fas ${icon} me-2 mt-1"></i>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold">${escapeHtml(notif.message)}</div>
                                                    <small class="text-muted">${notif.created_at}</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                `;
                            });
                            notificationList.html(html);
                        } else {
                            notificationList.html('<li class="dropdown-item-text text-center text-muted py-3">No notifications</li>');
                        }
                    }
                },
                error: function() {
                    $('#notificationList').html('<li class="dropdown-item-text text-center text-danger py-3">Failed to load notifications</li>');
                }
            });
        }

        // Mark all notifications as read when bell dropdown is shown
        $('#notificationDropdown').on('show.bs.dropdown', function() {
            const badge = $('#notificationBadge');
            const unreadCount = parseInt(badge.text()) || 0;
            
            if (unreadCount > 0) {
                // Mark all as read via AJAX
                $.ajax({
                    url: '<?= base_url('notifications/mark_all_read') ?>',
                    method: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Hide badge
                            badge.hide();
                            // Remove unread styling from all notifications
                            $('.notification-item.unread').removeClass('unread bg-light');
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to mark notifications as read:', xhr.statusText);
                    }
                });
            }
        });

        // Mark all as read button click handler
        $('#markAllRead').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            $.ajax({
                url: '<?= base_url('notifications/mark_all_read') ?>',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Hide badge
                        $('#notificationBadge').hide();
                        // Remove unread styling from all notifications
                        $('.notification-item.unread').removeClass('unread bg-light');
                        // Reload notifications to update UI
                        loadNotifications();
                    }
                },
                error: function(xhr) {
                    console.error('Failed to mark all as read:', xhr.statusText);
                }
            });
        });

        // Course actions panel toggle functionality
        const baseUrl = '<?= base_url() ?>';

        // Chevron rotation for Bootstrap accordion - uses native collapse events
        // Bootstrap handles accordion behavior via data-bs-toggle and data-bs-parent
        document.addEventListener('DOMContentLoaded', function() {
            // DEBUG: Check for duplicate IDs
            const collapseElements = document.querySelectorAll('[id^="actions-"]');
            const ids = Array.from(collapseElements).map(el => el.id);
            const duplicates = ids.filter((id, index) => ids.indexOf(id) !== index);
            if (duplicates.length > 0) {
                console.error('DUPLICATE COLLAPSE IDs FOUND:', duplicates);
            } else {
                console.log('✓ All collapse IDs are unique. Total:', ids.length);
            }
            
            // Listen to Bootstrap collapse events to rotate chevron icons
            collapseElements.forEach(function(collapseEl) {
                collapseEl.addEventListener('show.bs.collapse', function() {
                    console.log('Opening:', this.id);
                    const btn = document.querySelector('[data-bs-target="#' + this.id + '"]');
                    if (btn) {
                        btn.classList.add('rotated');
                    }
                });
                
                collapseEl.addEventListener('hide.bs.collapse', function() {
                    console.log('Closing:', this.id);
                    const btn = document.querySelector('[data-bs-target="#' + this.id + '"]');
                    if (btn) {
                        btn.classList.remove('rotated');
                    }
                });
            });
        });

        // Modal Functions
        function openStudentsModal(courseId, courseCode) {
            $('#studentsModalLabel').html(`<i class="fas fa-user-graduate me-2"></i>Students - ${escapeHtml(courseCode)}`);
            $('#studentsModalCourseId').val(courseId);
            $('#studentsModal').modal('show');
            loadStudents(courseId);
        }

        function openQuizzesModal(courseId, courseCode) {
            $('#quizzesModalLabel').html(`<i class="fas fa-question-circle me-2"></i>Quizzes - ${escapeHtml(courseCode)}`);
            $('#quizzesModalCourseId').val(courseId);
            $('#quizzesModal').modal('show');
            loadQuizzes(courseId);
        }

        function openAssignmentsModal(courseId, courseCode) {
            $('#assignmentsModalLabel').html(`<i class="fas fa-tasks me-2"></i>Assignments - ${escapeHtml(courseCode)}`);
            $('#assignmentsModalCourseId').val(courseId);
            $('#assignmentsModal').modal('show');
            loadAssignments(courseId);
        }

        // Load Students
        function loadStudents(courseId) {
            const container = $('#studentsListContainer');
            container.html('<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>');
            
            $.ajax({
                url: baseUrl + 'teacher/course/' + courseId + '/students',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderStudentsList(response.students);
                    } else {
                        container.html('<div class="alert alert-warning">' + (response.message || 'No students enrolled yet.') + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load students:', xhr, status, error);
                    console.error('Response:', xhr.responseText);
                    let errorMsg = 'Failed to load students.';
                    if (xhr.status === 401) {
                        errorMsg = 'Unauthorized - Please log in again.';
                    } else if (xhr.status === 403) {
                        errorMsg = 'Access denied - You do not own this course.';
                    } else if (xhr.status === 404) {
                        errorMsg = 'Endpoint not found (404).';
                    }
                    container.html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        }

        function renderStudentsList(students) {
            const container = $('#studentsListContainer');
            if (!students || students.length === 0) {
                container.html('<div class="alert alert-info">No students enrolled or requesting enrollment in this course.</div>');
                return;
            }

            // Separate students by status
            const pendingStudents = students.filter(s => s.status === 'pending');
            const enrolledStudents = students.filter(s => s.status === 'enrolled');

            let html = '';

            // Pending Requests Section
            if (pendingStudents.length > 0) {
                html += '<div class="mb-4">';
                html += '<h6 class="text-warning"><i class="fas fa-clock me-2"></i>Pending Enrollment Requests (' + pendingStudents.length + ')</h6>';
                html += '<div class="table-responsive"><table class="table table-hover"><thead><tr>' +
                    '<th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
                
                pendingStudents.forEach(student => {
                    html += `<tr data-enrollment-id="${student.enrollment_id}">
                        <td><i class="fas fa-user-circle me-2 text-warning"></i>${escapeHtml(student.name)}</td>
                        <td>${escapeHtml(student.email)}</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td>
                            <button class="btn btn-sm btn-success approve-btn me-1" data-enrollment-id="${student.enrollment_id}" data-student-name="${escapeHtml(student.name)}">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-sm btn-danger reject-btn" data-enrollment-id="${student.enrollment_id}" data-student-name="${escapeHtml(student.name)}">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </td>
                    </tr>`;
                });
                html += '</tbody></table></div></div>';
            }

            // Enrolled Students Section
            if (enrolledStudents.length > 0) {
                html += '<div>';
                html += '<h6 class="text-success"><i class="fas fa-user-check me-2"></i>Enrolled Students (' + enrolledStudents.length + ')</h6>';
                html += '<div class="table-responsive"><table class="table table-hover" id="enrolledStudentsTable"><thead><tr>' +
                    '<th>Name</th><th>Email</th><th>Enrollment Date</th><th>Status</th></tr></thead><tbody>';
                
                enrolledStudents.forEach(student => {
                    const enrollDate = student.enrollment_date ? new Date(student.enrollment_date).toLocaleDateString() : 'N/A';
                    html += `<tr>
                        <td><i class="fas fa-user-circle me-2 text-success"></i>${escapeHtml(student.name)}</td>
                        <td>${escapeHtml(student.email)}</td>
                        <td>${enrollDate}</td>
                        <td><span class="badge bg-success">Enrolled</span></td>
                    </tr>`;
                });
                html += '</tbody></table></div></div>';
            }

            container.html(html);
            
            // Add search functionality
            $('#studentSearch').off('keyup').on('keyup', function() {
                const value = $(this).val().toLowerCase();
                $('#studentsListContainer table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            // Approve button handler
            $('.approve-btn').off('click').on('click', function() {
                const enrollmentId = $(this).data('enrollment-id');
                const studentName = $(this).data('student-name');
                
                if (confirm(`Approve enrollment for ${studentName}?`)) {
                    approveEnrollment(enrollmentId, $(this).closest('tr'));
                }
            });

            // Reject button handler
            $('.reject-btn').off('click').on('click', function() {
                const enrollmentId = $(this).data('enrollment-id');
                const studentName = $(this).data('student-name');
                
                if (confirm(`Reject enrollment request from ${studentName}?`)) {
                    rejectEnrollment(enrollmentId, $(this).closest('tr'));
                }
            });
        }

        // Load Quizzes
        function loadQuizzes(courseId) {
            const container = $('#quizzesListContainer');
            container.html('<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>');
            
            $.ajax({
                url: baseUrl + 'teacher/quizzes/' + courseId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderQuizzesList(response.quizzes);
                    } else {
                        const courseId = $('#quizzesModalCourseId').val();
                        container.html('<div class="alert alert-warning">' + (response.message || 'No quizzes created yet.') + ' <button class="btn btn-sm btn-primary ms-2" onclick="showCreateQuizForm(' + courseId + ')">Create Quiz</button></div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load quizzes:', xhr, status, error);
                    console.error('Response:', xhr.responseText);
                    console.error('Status code:', xhr.status);
                    
                    let errorMsg = 'Failed to load quizzes.';
                    
                    // Try to parse JSON error response
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMsg = response.message;
                        }
                    } catch (e) {
                        // Not JSON, use status code
                        if (xhr.status === 401) {
                            errorMsg = 'Unauthorized - Please log in again.';
                        } else if (xhr.status === 403) {
                            errorMsg = 'Access denied - You do not own this course. (Status: ' + xhr.status + ')';
                        } else if (xhr.status === 404) {
                            errorMsg = 'Endpoint not found (404). URL: ' + baseUrl + 'teacher/course/' + courseId + '/quizzes';
                        } else if (xhr.status === 500) {
                            errorMsg = 'Server error (500). Check server logs.';
                        } else {
                            errorMsg = 'Failed to load quizzes. Status: ' + xhr.status + ', Error: ' + error;
                        }
                    }
                    
                    container.html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        }

        function renderQuizzesList(quizzes) {
            const container = $('#quizzesListContainer');
            const courseId = $('#quizzesModalCourseId').val();
            
            if (!quizzes || quizzes.length === 0) {
                container.html('<div class="alert alert-info">No quizzes created yet. <button class="btn btn-sm btn-primary ms-2" onclick="showCreateQuizForm(' + courseId + ')">Create Quiz</button></div>');
                return;
            }

            let html = '<div class="mb-3"><button class="btn btn-primary btn-sm" onclick="showCreateQuizForm(' + courseId + ')"><i class="fas fa-plus me-1"></i>Create New Quiz</button></div>';
            html += '<div class="list-group">';
            
            quizzes.forEach(quiz => {
                const submissions = quiz.submissions || 0;
                const passed = quiz.passed || 0;
                html += `<div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${escapeHtml(quiz.title)}</h6>
                            <p class="mb-1 text-muted small">${escapeHtml(quiz.description || 'No description')}</p>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>${quiz.created_at || 'N/A'} | 
                                <i class="fas fa-check-circle text-success ms-2 me-1"></i>${passed} passed | 
                                <i class="fas fa-paper-plane text-primary ms-2 me-1"></i>${submissions} submitted
                            </small>
                        </div>
                        <div class="btn-group-vertical btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="viewQuizSubmissions(${quiz.id})"><i class="fas fa-eye me-1"></i> View Submissions</button>
                            <button class="btn btn-outline-danger" onclick="deleteQuiz(${quiz.id})"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.html(html);
        }

        // Load Assignments
        function loadAssignments(courseId) {
            const container = $('#assignmentsListContainer');
            container.html('<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>');
            
            $.ajax({
                url: baseUrl + 'assignments/view/' + courseId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderAssignmentsList(response.assignments, courseId);
                    } else {
                        container.html('<div class="alert alert-warning">' + (response.message || 'No assignments created yet.') + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load assignments:', xhr, status, error);
                    console.error('Response:', xhr.responseText);
                    let errorMsg = 'Failed to load assignments.';
                    if (xhr.status === 401) {
                        errorMsg = 'Unauthorized - Please log in again.';
                    } else if (xhr.status === 403) {
                        errorMsg = 'Access denied - You do not own this course.';
                    } else if (xhr.status === 404) {
                        errorMsg = 'Endpoint not found (404).';
                    }
                    container.html('<div class="alert alert-danger">' + errorMsg + '</div>');
                }
            });
        }

        function renderAssignmentsList(assignments, courseId) {
            const container = $('#assignmentsListContainer');
            if (!assignments || assignments.length === 0) {
                container.html('<div class="alert alert-info">No assignments created yet. <button class="btn btn-sm btn-primary ms-2" onclick="showCreateAssignmentForm(' + courseId + ')">Create Assignment</button></div>');
                return;
            }

            let html = '<div class="mb-3"><button class="btn btn-primary btn-sm" onclick="showCreateAssignmentForm(' + courseId + ')"><i class="fas fa-plus me-1"></i>Create New Assignment</button></div>';
            html += '<div class="list-group">';
            
            assignments.forEach(assignment => {
                const submissionCount = assignment.submission_count || 0;
                const dueDate = assignment.due_date ? new Date(assignment.due_date).toLocaleDateString() : 'No due date';
                const hasFile = assignment.file_name ? true : false;
                
                html += `<div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${escapeHtml(assignment.title)}</h6>
                            <p class="mb-1 text-muted small">${escapeHtml(assignment.description || 'No description')}</p>
                            <small class="text-muted">
                                <i class="fas fa-calendar-check me-1"></i>Due: ${dueDate} | 
                                <i class="fas fa-tasks text-info ms-2 me-1"></i>Total Score: ${assignment.total_score || 'N/A'} | 
                                <i class="fas fa-paper-plane text-primary ms-2 me-1"></i>${submissionCount} submission(s)
                            </small>`;
                
                if (hasFile) {
                    html += `<div class="mt-2">
                        <a href="${baseUrl}assignments/download/${assignment.id}" class="btn btn-sm btn-outline-success" target="_blank">
                            <i class="fas fa-download me-1"></i>${escapeHtml(assignment.file_name)}
                        </a>
                    </div>`;
                }
                
                html += `</div>
                        <div class="btn-group-vertical btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="viewSubmissions(${assignment.id})" title="View Submissions">
                                <i class="fas fa-users"></i>
                            </button>
                            <button class="btn btn-outline-danger" onclick="deleteAssignment(${assignment.id}, ${courseId})" title="Delete Assignment">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.html(html);
        }

        // Quiz Functions
        let questionCounter = 0;
        
        function showCreateQuizForm(courseId) {
            $('#quizzesModal').modal('hide');
            $('#createQuizCourseId').val(courseId);
            $('#createQuizForm')[0].reset();
            $('#questionsContainer').empty();
            questionCounter = 0;
            addQuestion(); // Add first question by default
            $('#createQuizModal').modal('show');
        }

        function addQuestion() {
            questionCounter++;
            const questionHtml = `
                <div class="card mb-3 question-item" data-question-index="${questionCounter}">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong>Question ${questionCounter}</strong>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeQuestion(${questionCounter})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Question Text <span class="text-danger">*</span></label>
                            <textarea class="form-control question-text" rows="3" required></textarea>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Question Type <span class="text-danger">*</span></label>
                                <select class="form-select question-type" onchange="toggleQuestionType(${questionCounter})" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True or False</option>
                                    <option value="sentence">Sentence Answer</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Points <span class="text-danger">*</span></label>
                                <input type="number" class="form-control question-points" min="1" value="1" required>
                            </div>
                        </div>
                        
                        <!-- Multiple Choice Options (hidden by default) -->
                        <div class="mcq-options" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label">Option A <span class="text-danger">*</span></label>
                                <input type="text" class="form-control option-a">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Option B <span class="text-danger">*</span></label>
                                <input type="text" class="form-control option-b">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Option C <span class="text-danger">*</span></label>
                                <input type="text" class="form-control option-c">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Option D <span class="text-danger">*</span></label>
                                <input type="text" class="form-control option-d">
                            </div>
                            <div class="mb-2">
                                <label class="form-label">Correct Answer <span class="text-danger">*</span></label>
                                <select class="form-select correct-answer-mcq">
                                    <option value="">-- Select --</option>
                                    <option value="A">Option A</option>
                                    <option value="B">Option B</option>
                                    <option value="C">Option C</option>
                                    <option value="D">Option D</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- True/False Options (hidden by default) -->
                        <div class="tf-options" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label">Correct Answer <span class="text-danger">*</span></label>
                                <select class="form-select correct-answer-tf">
                                    <option value="">-- Select --</option>
                                    <option value="A">True</option>
                                    <option value="B">False</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Sentence Answer (hidden by default) -->
                        <div class="sentence-answer" style="display: none;">
                            <div class="mb-2">
                                <label class="form-label">Expected Answer (Optional - for teacher reference)</label>
                                <textarea class="form-control correct-answer-sentence" rows="2" placeholder="This will not be shown to students"></textarea>
                                <small class="text-muted">This answer will be graded manually by you after submission.</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#questionsContainer').append(questionHtml);
        }

        function removeQuestion(index) {
            $(`.question-item[data-question-index="${index}"]`).remove();
        }

        function toggleQuestionType(index) {
            const $question = $(`.question-item[data-question-index="${index}"]`);
            const type = $question.find('.question-type').val();
            
            if (type === 'multiple_choice') {
                $question.find('.mcq-options').show();
                $question.find('.tf-options, .sentence-answer').hide();
                $question.find('.mcq-options input, .mcq-options select').prop('required', true);
                $question.find('.tf-options select, .sentence-answer textarea').prop('required', false);
            } else if (type === 'true_false') {
                $question.find('.tf-options').show();
                $question.find('.mcq-options, .sentence-answer').hide();
                $question.find('.tf-options select').prop('required', true);
                $question.find('.mcq-options input, .mcq-options select, .sentence-answer textarea').prop('required', false);
            } else if (type === 'sentence') {
                $question.find('.sentence-answer').show();
                $question.find('.mcq-options, .tf-options').hide();
                $question.find('.mcq-options input, .mcq-options select, .tf-options select').prop('required', false);
                $question.find('.sentence-answer textarea').prop('required', false);
            } else {
                $question.find('.mcq-options, .tf-options, .sentence-answer').hide();
            }
        }

        function createQuiz() {
            const courseId = $('#createQuizCourseId').val();
            const title = $('#quizTitle').val();
            const description = $('#quizDescription').val();

            if (!title) {
                alert('Please enter a quiz title.');
                return;
            }

            // Collect all questions
            const questions = [];
            let valid = true;
            
            $('.question-item').each(function() {
                const $q = $(this);
                const questionText = $q.find('.question-text').val();
                const questionType = $q.find('.question-type').val();
                const points = $q.find('.question-points').val();

                if (!questionText || !questionType || !points) {
                    alert('Please fill in all required fields for each question.');
                    valid = false;
                    return false;
                }

                const questionData = {
                    question: questionText,
                    question_type: questionType,
                    points: parseInt(points)
                };

                if (questionType === 'multiple_choice') {
                    questionData.option_a = $q.find('.option-a').val();
                    questionData.option_b = $q.find('.option-b').val();
                    questionData.option_c = $q.find('.option-c').val();
                    questionData.option_d = $q.find('.option-d').val();
                    questionData.correct_answer = $q.find('.correct-answer-mcq').val();

                    if (!questionData.option_a || !questionData.option_b || !questionData.option_c || !questionData.option_d || !questionData.correct_answer) {
                        alert('Multiple choice questions require all 4 options and a correct answer.');
                        valid = false;
                        return false;
                    }
                } else if (questionType === 'true_false') {
                    questionData.correct_answer = $q.find('.correct-answer-tf').val();

                    if (!questionData.correct_answer) {
                        alert('True/False questions require a correct answer.');
                        valid = false;
                        return false;
                    }
                } else if (questionType === 'sentence') {
                    questionData.correct_answer = $q.find('.correct-answer-sentence').val();
                }

                questions.push(questionData);
            });

            if (!valid) return;
            if (questions.length === 0) {
                alert('Please add at least one question.');
                return;
            }

            const data = {
                course_id: courseId,
                title: title,
                description: description,
                questions: questions
            };

            $.ajax({
                url: baseUrl + 'teacher/quiz/create',
                method: 'POST',
                data: JSON.stringify(data),
                contentType: 'application/json',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#createQuizModal').modal('hide');
                        $('#quizzesModal').modal('show');
                        loadQuizzes(courseId);
                        alert(response.message || 'Quiz created successfully!');
                    } else {
                        alert(response.message || 'Failed to create quiz.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to create quiz:', xhr, status, error);
                    alert('Failed to create quiz. Please try again.');
                }
            });
        }

        function viewQuizSubmissions(quizId) {
            $('#quizzesModal').modal('hide');
            $('#viewQuizSubmissionsId').val(quizId);
            $('#viewQuizSubmissionsModal').modal('show');
            loadQuizSubmissions(quizId);
        }

        function loadQuizSubmissions(quizId) {
            const container = $('#quizSubmissionsListContainer');
            container.html('<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>');

            $.ajax({
                url: baseUrl + 'teacher/quiz/' + quizId + '/submissions',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderQuizSubmissionsList(response.submissions, response.questions);
                    } else {
                        container.html('<div class="alert alert-warning">' + (response.message || 'No submissions yet.') + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load submissions:', xhr, status, error);
                    container.html('<div class="alert alert-danger">Failed to load submissions.</div>');
                }
            });
        }

        function renderQuizSubmissionsList(submissions, questions) {
            const container = $('#quizSubmissionsListContainer');
            if (!submissions || submissions.length === 0) {
                container.html('<div class="alert alert-info">No submissions yet.</div>');
                return;
            }

            let html = '';
            submissions.forEach(sub => {
                const score = sub.score !== null ? sub.score.toFixed(2) + '%' : 'Pending Grading';
                const badgeClass = sub.score !== null ? (sub.score >= 75 ? 'bg-success' : 'bg-danger') : 'bg-warning';
                
                html += `<div class="card mb-3">
                    <div class="card-header">
                        <strong>${escapeHtml(sub.student_name)}</strong>
                        <span class="badge ${badgeClass} float-end">${score}</span>
                    </div>
                    <div class="card-body">`;

                sub.answers.forEach((answer, idx) => {
                    const question = questions[idx];
                    html += `<div class="mb-3 border-bottom pb-2">
                        <p><strong>Q${idx + 1}:</strong> ${escapeHtml(question.question)}</p>
                        <p><strong>Answer:</strong> ${escapeHtml(answer.student_answer)}</p>`;
                    
                    if (question.question_type === 'sentence') {
                        // Show grading button for sentence type
                        html += `<button class="btn btn-sm btn-primary" onclick="showGradeModal(${answer.id}, ${sub.id}, '${escapeHtml(answer.student_answer)}', ${question.points})">
                            <i class="fas fa-edit me-1"></i>Grade (${answer.points_earned || 0}/${question.points} pts)
                        </button>`;
                    } else {
                        // Show if correct for MCQ
                        const correctBadge = answer.is_correct ? '<span class="badge bg-success">Correct</span>' : '<span class="badge bg-danger">Incorrect</span>';
                        html += `<p>${correctBadge} - ${answer.points_earned}/${question.points} pts</p>`;
                    }
                    html += `</div>`;
                });

                html += `</div></div>`;
            });

            container.html(html);
        }

        function showGradeModal(answerId, submissionId, studentAnswer, maxPoints) {
            $('#gradeAnswerId').val(answerId);
            $('#gradeSubmissionId').val(submissionId);
            $('#gradeMaxPoints').val(maxPoints);
            $('#studentAnswerText').text(studentAnswer);
            $('#maxPointsDisplay').text(maxPoints);
            $('#pointsEarned').attr('max', maxPoints).val(0);
            $('#gradeSentenceModal').modal('show');
        }

        function saveGrade() {
            const answerId = $('#gradeAnswerId').val();
            const submissionId = $('#gradeSubmissionId').val();
            const pointsEarned = $('#pointsEarned').val();
            const maxPoints = $('#gradeMaxPoints').val();

            if (parseInt(pointsEarned) > parseInt(maxPoints)) {
                alert('Points earned cannot exceed maximum points.');
                return;
            }

            $.ajax({
                url: baseUrl + 'teacher/quiz/grade-answer',
                method: 'POST',
                data: {
                    answer_id: answerId,
                    submission_id: submissionId,
                    points_earned: pointsEarned
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#gradeSentenceModal').modal('hide');
                        loadQuizSubmissions($('#viewQuizSubmissionsId').val());
                        alert('Answer graded successfully!');
                    } else {
                        alert(response.message || 'Failed to grade answer.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to grade answer:', xhr, status, error);
                    alert('Failed to grade answer. Please try again.');
                }
            });
        }

        function deleteQuiz(id) {
            if (!confirm('Are you sure you want to delete this quiz? All submissions will also be deleted.')) {
                return;
            }

            $.ajax({
                url: baseUrl + 'teacher/quiz/delete/' + id,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        const courseId = $('#quizzesModalCourseId').val();
                        loadQuizzes(courseId);
                        alert(response.message || 'Quiz deleted successfully.');
                    } else {
                        alert(response.message || 'Failed to delete quiz.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to delete quiz:', xhr, status, error);
                    alert('Failed to delete quiz. Please try again.');
                }
            });
        }

        // Assignment Functions
        function showCreateAssignmentForm(courseId) {
            $('#assignmentsModal').modal('hide');
            $('#createAssignmentCourseId').val(courseId);
            $('#createAssignmentForm')[0].reset();
            $('#createAssignmentModal').modal('show');
        }

        function createAssignment() {
            const courseId = $('#createAssignmentCourseId').val();
            const formData = new FormData($('#createAssignmentForm')[0]);
            
            $.ajax({
                url: baseUrl + 'assignments/create/' + courseId,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Assignment created successfully!');
                        $('#createAssignmentModal').modal('hide');
                        $('#assignmentsModal').modal('show');
                        loadAssignments(courseId);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to create assignment.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to create assignment:', xhr, status, error);
                    alert('Failed to create assignment. Please check the form and try again.');
                }
            });
        }

        function deleteAssignment(assignmentId, courseId) {
            if (!confirm('Are you sure you want to delete this assignment? All student submissions will also be deleted.')) {
                return;
            }
            
            $.ajax({
                url: baseUrl + 'assignments/delete/' + assignmentId,
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Assignment deleted successfully!');
                        loadAssignments(courseId);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to delete assignment.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to delete assignment:', xhr, status, error);
                    alert('Failed to delete assignment. Please try again.');
                }
            });
        }

        function viewSubmissions(assignmentId) {
            $('#assignmentsModal').modal('hide');
            $('#viewSubmissionsAssignmentId').val(assignmentId);
            loadSubmissions(assignmentId);
            $('#submissionsModal').modal('show');
        }

        function loadSubmissions(assignmentId) {
            const container = $('#submissionsListContainer');
            container.html('<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>');
            
            $.ajax({
                url: baseUrl + 'submissions/view/' + assignmentId,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        renderSubmissionsList(response.submissions, assignmentId);
                    } else {
                        container.html('<div class="alert alert-warning">' + (response.message || 'No submissions yet.') + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load submissions:', xhr, status, error);
                    container.html('<div class="alert alert-danger">Failed to load submissions.</div>');
                }
            });
        }

        function renderSubmissionsList(submissions, assignmentId) {
            const container = $('#submissionsListContainer');
            if (!submissions || submissions.length === 0) {
                container.html('<div class="alert alert-info">No submissions received yet.</div>');
                return;
            }

            let html = '<div class="list-group">';
            submissions.forEach(submission => {
                const submittedDate = submission.submitted_at ? new Date(submission.submitted_at).toLocaleString() : 'N/A';
                const gradedDate = submission.graded_at ? new Date(submission.graded_at).toLocaleString() : 'Not graded';
                
                // Dynamic status badge
                let statusBadge = '';
                if (submission.status === 'graded') {
                    statusBadge = '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Graded</span>';
                } else {
                    statusBadge = '<span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Pending</span>';
                }
                
                const hasFile = submission.file_name ? true : false;
                
                html += `<div class="list-group-item" id="submission_${submission.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${escapeHtml(submission.student_name)} ${statusBadge}</h6>
                            <p class="mb-1 text-muted small"><i class="fas fa-calendar-alt me-1"></i>Submitted: ${submittedDate}</p>`;
                
                if (submission.text_submission) {
                    html += `<p class="mb-1"><strong>Text Submission:</strong> ${escapeHtml(submission.text_submission)}</p>`;
                }
                
                if (hasFile) {
                    html += `<div class="mb-2">
                        <a href="${baseUrl}submissions/download/${submission.id}" class="btn btn-sm btn-outline-info" target="_blank">
                            <i class="fas fa-download me-1"></i>${escapeHtml(submission.file_name)}
                        </a>
                    </div>`;
                }
                
                if (submission.status === 'graded') {
                    html += `<p class="mb-1"><strong>Score:</strong> <span class="text-success fw-bold">${submission.score || 'N/A'}</span> | <strong>Graded:</strong> ${gradedDate}</p>`;
                } else {
                    html += `<div class="mt-2">
                        <button class="btn btn-sm btn-primary" onclick="showGradeForm(${submission.id}, ${assignmentId})">
                            <i class="fas fa-edit me-1"></i>Grade Submission
                        </button>
                    </div>`;
                }
                
                html += `</div>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.html(html);
        }

        function showGradeForm(submissionId, assignmentId) {
            $('#gradeSubmissionId').val(submissionId);
            $('#gradeAssignmentId').val(assignmentId);
            $('#gradeScore').val('');
            $('#gradeSubmissionModal').modal('show');
        }

        function gradeSubmission() {
            const submissionId = $('#gradeSubmissionId').val();
            const assignmentId = $('#gradeAssignmentId').val();
            const score = $('#gradeScore').val();
            
            if (!score || isNaN(score)) {
                alert('Please enter a valid score.');
                return;
            }
            
            $.ajax({
                url: baseUrl + 'submissions/grade/' + submissionId,
                method: 'POST',
                data: { score: score },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Submission graded successfully!');
                        $('#gradeSubmissionModal').modal('hide');
                        loadSubmissions(assignmentId);
                    } else {
                        alert('Error: ' + (response.message || 'Failed to grade submission.'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to grade submission:', xhr, status, error);
                    alert('Failed to grade submission. Please try again.');
                }
            });
        }

        function viewAssignment(id) {
            alert('View Assignment ' + id + ' details.');

        }

        function editAssignment(id) {
            alert('Edit Assignment ' + id + ' form will be implemented here.');
        }

        async function loadSubjects(courseId) {
            const container = document.getElementById('subjects-' + courseId);
            
            try {
                const response = await fetch(baseUrl + 'teacher/course/' + courseId + '/subjects');
                const data = await response.json();
                
                if (data.status === 'success') {
                    loadedCourses.add(courseId);
                    renderSubjects(container, data);
                } else {
                    container.innerHTML = `
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            ${escapeHtml(data.message || 'Failed to load subjects')}
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error loading subjects:', error);
                container.innerHTML = `
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        Error loading subjects. Please try again.
                    </div>
                `;
            }
        }

        function renderSubjects(container, data) {
            const subjects = data.subjects || [];
            
            if (subjects.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No subjects found for this course yet.
                    </div>
                `;
                return;
            }
            
            let html = '<h6 class="mb-3"><i class="fas fa-book-open me-2"></i>Subjects/Lessons</h6>';
            
            subjects.forEach(subject => {
                const quizCount = subject.quiz_count || 0;
                const duration = subject.duration_minutes ? `${subject.duration_minutes} min` : 'N/A';
                
                html += `
                    <div class="subject-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${escapeHtml(subject.title)}</h6>
                                ${subject.content ? `<p class="small text-muted mb-2">${escapeHtml(subject.content.substring(0, 100))}${subject.content.length > 100 ? '...' : ''}</p>` : ''}
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2">
                            <span class="badge bg-info">
                                <i class="fas fa-question-circle me-1"></i>${quizCount} Quiz${quizCount !== 1 ? 'zes' : ''}
                            </span>
                            <span class="badge bg-secondary">
                                <i class="fas fa-clock me-1"></i>${duration}
                            </span>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Approve enrollment function
        function approveEnrollment(enrollmentId, rowElement) {
            $.ajax({
                url: '<?= base_url('teacher/enroll/approve') ?>',
                method: 'POST',
                data: { enrollment_id: enrollmentId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Show success message
                        showAlert('success', response.message);
                        // Remove the row with animation
                        rowElement.fadeOut(300, function() {
                            $(this).remove();
                            // Reload students list to update counts
                            const courseId = $('#studentsModalCourseId').val();
                            loadStudents(courseId);
                        });
                        // Reload notifications
                        loadNotifications();
                    } else {
                        showAlert('danger', response.message || 'Failed to approve enrollment');
                    }
                },
                error: function() {
                    showAlert('danger', 'An error occurred. Please try again.');
                }
            });
        }

        // Reject enrollment function
        function rejectEnrollment(enrollmentId, rowElement) {
            $.ajax({
                url: '<?= base_url('teacher/enroll/reject') ?>',
                method: 'POST',
                data: { enrollment_id: enrollmentId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Show success message
                        showAlert('warning', response.message);
                        // Remove the row with animation
                        rowElement.fadeOut(300, function() {
                            $(this).remove();
                            // Reload students list to update counts
                            const courseId = $('#studentsModalCourseId').val();
                            loadStudents(courseId);
                        });
                        // Reload notifications
                        loadNotifications();
                    } else {
                        showAlert('danger', response.message || 'Failed to reject enrollment');
                    }
                },
                error: function() {
                    showAlert('danger', 'An error occurred. Please try again.');
                }
            });
        }

        // Show alert helper function
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('#studentsListContainer').prepend(alertHtml);
            setTimeout(function() {
                $('.alert').fadeOut(300, function() { $(this).remove(); });
            }, 3000);
        }

        // Optional: Click on card to toggle accordion (uses Bootstrap native behavior)
        document.querySelectorAll('.course-card').forEach(card => {
            card.addEventListener('click', function(e) {
                // Only trigger if NOT clicking a button (expand button handles itself)
                if (!e.target.closest('button')) {
                    const expandBtn = this.querySelector('.expand-btn');
                    if (expandBtn) {
                        expandBtn.click(); // Trigger Bootstrap's native collapse toggle
                    }
                }
            });
        });
    </script>

    <!-- Students Modal -->
    <div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="studentsModalLabel">Students</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="studentsModalCourseId">
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <input type="text" class="form-control" id="studentSearch" placeholder="Search students by name or email...">
                    </div>
                    <!-- Students List -->
                    <div id="studentsListContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quizzes Modal -->
    <div class="modal fade" id="quizzesModal" tabindex="-1" aria-labelledby="quizzesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizzesModalLabel">Quizzes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="quizzesModalCourseId">
                    <!-- Quizzes List -->
                    <div id="quizzesListContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Quiz Modal -->
    <div class="modal fade" id="createQuizModal" tabindex="-1" aria-labelledby="createQuizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createQuizModalLabel">Create New Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <form id="createQuizForm">
                        <input type="hidden" id="createQuizCourseId">
                        
                        <div class="mb-3">
                            <label for="quizTitle" class="form-label">Quiz Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="quizTitle" name="title" required maxlength="200">
                        </div>
                        
                        <div class="mb-3">
                            <label for="quizDescription" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="quizDescription" name="description" rows="2"></textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="mb-0">Questions</h6>
                            <button type="button" class="btn btn-sm btn-primary" onclick="addQuestion()">
                                <i class="fas fa-plus me-1"></i>Add Question
                            </button>
                        </div>
                        
                        <div id="questionsContainer">
                            <!-- Questions will be added dynamically here -->
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createQuiz()">Create Quiz</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Quiz Submissions Modal -->
    <div class="modal fade" id="viewQuizSubmissionsModal" tabindex="-1" aria-labelledby="viewQuizSubmissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewQuizSubmissionsModalLabel">Quiz Submissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="viewQuizSubmissionsId">
                    <div id="quizSubmissionsListContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Sentence Answer Modal -->
    <div class="modal fade" id="gradeSentenceModal" tabindex="-1" aria-labelledby="gradeSentenceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradeSentenceModalLabel">Grade Answer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="gradeAnswerId">
                    <input type="hidden" id="gradeSubmissionId">
                    <input type="hidden" id="gradeMaxPoints">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Student's Answer:</strong></label>
                        <p id="studentAnswerText" class="border p-2 bg-light"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="pointsEarned" class="form-label">Points Earned <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="pointsEarned" min="0" required>
                        <small class="form-text text-muted">Max: <span id="maxPointsDisplay"></span> points</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveGrade()">Save Grade</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments Modal -->
    <div class="modal fade" id="assignmentsModal" tabindex="-1" aria-labelledby="assignmentsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignmentsModalLabel">Assignments</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="assignmentsModalCourseId">
                    <!-- Assignments List -->
                    <div id="assignmentsListContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Assignment Modal -->
    <div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAssignmentModalLabel">Create New Assignment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createAssignmentForm">
                        <input type="hidden" id="createAssignmentCourseId">
                        
                        <div class="mb-3">
                            <label for="assignmentTitle" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="assignmentTitle" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="assignmentDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="assignmentDescription" name="description" rows="4"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="assignmentDueDate" class="form-label">Due Date <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control" id="assignmentDueDate" name="due_date" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="assignmentTotalScore" class="form-label">Total Score <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="assignmentTotalScore" name="total_score" min="1" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="assignmentFile" class="form-label">Attach File (Optional)</label>
                            <input type="file" class="form-control" id="assignmentFile" name="assignment_file" 
                                   accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.jpg,.jpeg,.png">
                            <small class="form-text text-muted">Max 10MB. Allowed: PDF, DOC, DOCX, PPT, PPTX, ZIP, JPG, JPEG, PNG</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="createAssignment()">Create Assignment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Submissions Modal -->
    <div class="modal fade" id="submissionsModal" tabindex="-1" aria-labelledby="submissionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="submissionsModalLabel">Student Submissions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="viewSubmissionsAssignmentId">
                    <div id="submissionsListContainer"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Submission Modal -->
    <div class="modal fade" id="gradeSubmissionModal" tabindex="-1" aria-labelledby="gradeSubmissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="gradeSubmissionModalLabel">Grade Submission</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="gradeSubmissionId">
                    <input type="hidden" id="gradeAssignmentId">
                    
                    <div class="mb-3">
                        <label for="gradeScore" class="form-label">Score <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="gradeScore" name="score" min="0" step="0.01" required>
                        <small class="form-text text-muted">Enter the score for this submission.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="gradeSubmission()">Save Grade</button>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
