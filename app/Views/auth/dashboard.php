<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2027, #2c5364);
        }
        .section-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        .table {
            background: #22303a;
            color: #bfc9d4;
            border-radius: 8px;
            margin-bottom: 18px;
        }
        .table th {
            color: #fff;
            background: transparent;
            border: none;
        }
        .table td {
            background: transparent;
            border: none;
        }
        .stat-card {
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(31,38,135,0.10);
            color: #fff;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.2s;
        }
        .stat-card:hover {
            box-shadow: 0 6px 20px rgba(31,38,135,0.25);
        }
    </style>
</head>
<body>
<?= view('templates/header') ?> <!-- optional header -->

<div class="container-fluid px-0">
    <div class="row">
        <!-- Recent Activity (common for all roles) -->
        <div class="col-lg-8 col-md-10 p-4">
            <div class="section-title mb-2">
                <i class="fa fa-history me-2"></i>Recent Activity
            </div>
            <table class="table mb-0">
                <thead>
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
                            <td colspan="3" class="text-muted">No recent activity.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Role-based Widgets -->
        <div class="col-md-3 d-flex flex-column align-items-center" style="margin-top: 60px;">

            <?php if ($role === 'admin'): ?>
                <!-- Admin Dashboard -->
                <!-- Admin Dashboard Stat Card as Button -->
                <button type="button"
                        class="stat-card text-center py-4 mb-4 btn"
                        style="background: #19d3c5; width:100%;"
                        data-bs-toggle="modal"
                        data-bs-target="#usersModal">
                    <div class="mb-2">
                        <i class="fa fa-users fa-2x"></i>
                        <span class="fs-5 fw-bold ms-2">Total Users</span>
                    </div>
                    <div class="fs-1 fw-bold"><?= $totalUsers ?? 0 ?></div>
                </button>

                <!-- Users Modal -->
                <div class="modal fade" id="usersModal" tabindex="-1" aria-labelledby="usersModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content bg-dark text-white">
                      <div class="modal-header">
                        <h5 class="modal-title" id="usersModalLabel">Registered Users</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <!-- Users Table -->
                        <table class="table table-striped table-dark">
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
                                <td colspan="3" class="text-muted">No users found.</td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                        <!-- Add User Button -->
                        <button type="button" class="btn btn-success mt-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                          <i class="fa fa-user-plus me-1"></i> Add User
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Add User Modal -->
                <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content bg-dark text-white">
                      <div class="modal-header">
                        <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <form action="<?= base_url('admin/addUser') ?>" method="post">
                        <div class="modal-body">
                          <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" required>
                          </div>
                          <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                          </div>
                          <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-select" name="role" required>
                              <option value="admin">Admin</option>
                              <option value="teacher">Teacher</option>
                              <option value="student">Student</option>
                            </select>
                          </div>
                          <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-success">Add User</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>

                <!-- Total Courses Stat Card as Button -->
                <button type="button"
                        class="stat-card text-center py-4 mb-4 btn"
                        style="background: #13b3a7; width:100%;"
                        data-bs-toggle="modal"
                        data-bs-target="#coursesModal">
                    <div class="mb-2">
                        <i class="fa fa-book fa-2x"></i>
                        <span class="fs-5 fw-bold ms-2">Total Courses</span>
                    </div>
                    <div class="fs-1 fw-bold"><?= $totalCourses ?? 0 ?></div>
                </button>

                <!-- Courses Modal -->
                <div class="modal fade" id="coursesModal" tabindex="-1" aria-labelledby="coursesModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content bg-dark text-white">
                      <div class="modal-header">
                        <h5 class="modal-title" id="coursesModalLabel">Courses List</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <!-- Courses Table -->
                        <table class="table table-striped table-dark">
                          <thead>
                            <tr>
                              <th>Course Name</th>
                              <th>Description</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php if (!empty($coursesList)): foreach ($coursesList as $course): ?>
                              <tr>
                                <td><?= esc($course['name'] ?? $course['title'] ?? 'Unnamed Course') ?></td>
                                <td><?= esc($course['description'] ?? '') ?></td>
                                <td><?= esc($course['created_at'] ?? '') ?></td>
                              </tr>
                            <?php endforeach; else: ?>
                              <tr>
                                <td colspan="3" class="text-muted">No courses found.</td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

            <?php elseif ($role === 'teacher'): ?>
                <!-- Teacher Dashboard -->
                <!-- Teacher Dashboard Stat Card as Button -->
                <button type="button"
                        class="stat-card text-center py-4 mb-4 btn"
                        style="background: #ff9800; width:100%;"
                        data-bs-toggle="modal"
                        data-bs-target="#teacherCoursesModal">
                    <div class="mb-2">
                        <i class="fa fa-chalkboard-teacher fa-2x"></i>
                        <span class="fs-5 fw-bold ms-2">My Courses</span>
                    </div>
                    <ul class="list-unstyled">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <li><?= esc($course['name'] ?? $course['title'] ?? 'Unnamed Course') ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="text-muted">No courses assigned.</li>
                        <?php endif; ?>
                    </ul>
                </button>

                <!-- Teacher Courses Modal -->
                <div class="modal fade" id="teacherCoursesModal" tabindex="-1" aria-labelledby="teacherCoursesModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content bg-dark text-white">
                      <div class="modal-header">
                        <h5 class="modal-title" id="teacherCoursesModalLabel">My Courses</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <table class="table table-striped table-dark">
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
                                <td><?= esc($course['created_at'] ?? '') ?></td>
                              </tr>
                            <?php endforeach; else: ?>
                              <tr>
                                <td colspan="3" class="text-muted">No courses assigned.</td>
                              </tr>
                            <?php endif; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>

            <?php elseif ($role === 'student'): ?>
                <!-- Student Dashboard -->
                <div class="stat-card text-center py-4 mb-4" style="background: #4caf50; width:100%;">
                    <div class="mb-2">
                        <i class="fa fa-graduation-cap fa-2x"></i>
                        <span class="fs-5 fw-bold ms-2">Enrolled Courses</span>
                    </div>
                    <ul class="list-unstyled">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <li><?= esc($course['name']) ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="text-muted">Not enrolled in any courses.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
