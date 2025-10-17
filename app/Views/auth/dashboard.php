<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- <style>
        body { background: #f8f9fa; }
        .card { border: none; box-shadow: 0 1px 2px rgba(0,0,0,0.03);}
        .table { margin-bottom: 0; }
        .dashboard-title { font-weight: 600, letter-spacing: .5px; }
        .modal-content { border-radius: 10px; }
        .btn, .form-control, .form-select { border-radius: 6px; }
        .list-unstyled li { padding: 2px 0; }
    </style> -->
</head>
<body>
<?= view('templates/header') ?>

<div class="container py-5">
    <div class="row g-4">
        <!-- Recent Activity -->
        <div class="col-lg-8 col-md-12">
            <div class="card p-3">
                <div class="d-flex align-items-center mb-3">
                    <i class="fa fa-history text-primary me-2"></i>
                    <span class="dashboard-title">Recent Activity</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>User</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentActivity ?? [])): ?>
                                <?php foreach ($recentActivity as $activity): ?>
                                    <tr>
                                        <td><?= esc($activity['date']) ?></td>
                                        <td><?= esc($activity['user']) ?></td>
                                        <td><?= esc($activity['action']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-muted text-center">No recent activity.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Role-based Widgets -->
        <div class="col-lg-4 col-md-12">
            <?php if ($role === 'admin'): ?>
                <!-- Users Card -->
                <div class="card mb-3 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <span><i class="fa fa-users text-primary me-2"></i>Users</span>
                        <span class="fw-bold"><?= $totalUsers ?? 0 ?></span>
                    </div>
                    <button class="btn btn-outline-primary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#usersModal">
                        View All
                    </button>
                </div>
                <!-- Users Modal -->
                <div class="modal fade" id="usersModal" tabindex="-1" aria-labelledby="usersModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="usersModalLabel">Registered Users</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <table class="table table-sm">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Role</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!empty($users)): foreach ($users as $user): ?>
                              <tr>
                                <td><?= esc($user['name']) ?></td>
                                <td><?= esc($user['email']) ?></td>
                                <td><?= esc($user['role']) ?></td>
                              </tr>
                            <?php endforeach; else: ?>
                              <tr>
                                <td colspan="3" class="text-muted text-center">No users found.</td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                        <button class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                          <i class="fa fa-user-plus me-1"></i> Add User
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Add User Modal -->
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <form action="<?= base_url('admin/addUser') ?>" method="post">
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                              <option value="admin">Admin</option>
                              <option value="teacher">Teacher</option>
                              <option value="student">Student</option>
                            </select>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-success w-100">Add User</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- Courses Card -->
                <div class="card mb-3 p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <span><i class="fa fa-book text-secondary me-2"></i>Courses</span>
                        <span class="fw-bold"><?= $totalCourses ?? 0 ?></span>
                    </div>
                    <button class="btn btn-outline-secondary w-100 mt-3" data-bs-toggle="modal" data-bs-target="#coursesModal">
                        View All
                    </button>
                </div>
                <!-- Courses Modal -->
                <div class="modal fade" id="coursesModal" tabindex="-1" aria-labelledby="coursesModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="coursesModalLabel">Courses List</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <table class="table table-sm">
                          <thead>
                            <tr>
                              <th>Course Name</th>
                              <th>Description</th>
                              <th>Actions</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php
    // Ensure $adminCourses is always defined to prevent "undefined variable" errors
                              $adminCourses = isset($coursesList)
                              ? $coursesList
                            : (isset($courses) ? $courses : []);

                               if (!empty($adminCourses)):
                                 ?>
                                   <?php foreach ($adminCourses as $course): ?>

                                    <tr>
                                        <td><?= esc($course['name'] ?? $course['course_name'] ?? $course['title'] ?? 'Unnamed Course') ?></td>
                                        <td><?= esc($course['description'] ?? '') ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-outline-primary"
                                               href="<?= base_url('admin/course/' . intval($course['id']) . '/upload') ?>">
                                               Manage Materials
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-muted text-center">No courses assigned.</td>
                                </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
            <?php elseif ($role === 'teacher'): ?>
                <!-- Teacher Courses Card -->
                <div class="card mb-3 p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa fa-chalkboard-teacher text-warning me-2"></i>
                        <span>My Courses</span>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <li class="d-flex justify-content-between align-items-center mb-1">
                                    <span><?= esc($course['name'] ?? $course['title'] ?? 'Unnamed Course') ?></span>
                                    <a class="btn btn-sm btn-outline-primary"
                                       href="<?= base_url('admin/course/'.intval($course['id']).'/upload') ?>">
                                       Materials
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="text-muted">No courses assigned.</li>
                        <?php endif; ?>
                    </ul>
                    <button class="btn btn-outline-warning w-100 mt-3" data-bs-toggle="modal" data-bs-target="#teacherCoursesModal">
                        View Details
                    </button>
                </div>
                <!-- Teacher Courses Modal -->
                <div class="modal fade" id="teacherCoursesModal" tabindex="-1" aria-labelledby="teacherCoursesModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="teacherCoursesModalLabel">My Courses</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <table class="table table-sm">
                          <thead>
                            <tr>
                              <th>Course Name</th>
                              <th>Description</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!empty($courses)): foreach ($courses as $course): ?>
                              <tr>
                                <td><?= esc($course['name'] ?? $course['title'] ?? 'Unnamed Course') ?></td>
                                <td><?= esc($course['description'] ?? '') ?></td>
                              </tr>
                            <?php endforeach; else: ?>
                              <tr>
                                <td colspan="2" class="text-muted text-center">No courses assigned.</td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
            <?php elseif ($role === 'student'): ?>
                <!-- Student Courses Card -->
                <div class="card mb-3 p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa fa-graduation-cap text-success me-2"></i>
                        <span>Enrolled Courses</span>
                    </div>
                    <?php if (!empty($enrolledCourses)): ?>
                        <ul class="list-group mb-3">
                            <?php foreach ($enrolledCourses as $course): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <?= esc($course['name']) ?>
                                        <?php if (!empty($course['description'])): ?>
                                            <small class="text-muted d-block"><?= esc($course['description']) ?></small>
                                        <?php endif; ?>
                                    </span>
                                    <span class="badge bg-primary"><?= date('M d, Y', strtotime($course['enrollment_date'])) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">Not enrolled in any courses.</div>
                    <?php endif; ?>
                </div>

                <!-- Available Courses Card -->
                <div class="card mb-3 p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa fa-book-open text-secondary me-2"></i>
                        <span>Available Courses</span>
                    </div>
                    <?php if (!empty($availableCourses)): ?>
                        <ul class="list-group">
                            <?php foreach ($availableCourses as $course): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <?= esc($course['name']) ?>
                                        <?php if (!empty($course['description'])): ?>
                                            <small class="text-muted d-block"><?= esc($course['description']) ?></small>
                                        <?php endif; ?>
                                    </span>
                                    <button class="btn btn-sm btn-outline-success enroll-btn" data-course-id="<?= $course['id'] ?>">
                                        <i class="fa fa-plus"></i> Enroll
                                    </button>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">No available courses to enroll.</div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // Listen for enroll button click
    $('.enroll-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var courseId = $btn.data('course-id');
        $.post('<?= base_url('course/enroll') ?>', { course_id: courseId }, function(data) {
            // Remove any previous alert
            $('.enroll-alert').remove();
            // Show Bootstrap alert
            var alertType = data.status === 'success' ? 'alert-success' : 'alert-danger';
            var $alert = $('<div class="alert enroll-alert ' + alertType + ' mt-2" role="alert">' + data.message + '</div>');
            $btn.closest('.card').prepend($alert);
            if (data.status === 'success') {
                // Disable the button
                $btn.prop('disabled', true).text('Enrolled');
                // Optionally, move the course to the Enrolled Courses list
                var $li = $btn.closest('li');
                $li.find('.btn').remove();
                // Add badge with today's date
                var today = new Date();
                var badge = $('<span class="badge bg-primary ms-2">' +
                    today.toLocaleString('default', { month: 'short' }) + ' ' +
                    today.getDate().toString().padStart(2, '0') + ', ' +
                    today.getFullYear() +
                '</span>');
                $li.append(badge);
                // Move to enrolled list
                $('.card:contains("Enrolled Courses") .list-group').append($li);
            }
        }, 'json');
    });
});
</script>
</body>
</html>
