<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($role ?? 'User') ?> Dashboard - LMS</title>
    <?= csrf_meta() ?>
    
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
        .header-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border-radius: 10px;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .card-header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            border: none;
        }
        .badge-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 6px 12px;
            border-radius: 20px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #6a3d8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
        }
        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
            color: white;
        }
        .list-group-item {
            border-radius: 6px !important;
            margin-bottom: 8px;
            transition: all 0.2s ease;
        }
        .list-group-item:hover {
            background: #f8f9fa;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        .modal-content {
            border: none;
            border-radius: 10px;
        }
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px 10px 0 0;
            border: none;
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .expand-btn {
            transition: transform 0.3s ease;
        }
        .expand-btn.rotated {
            transform: rotate(180deg);
        }
        /* Course Card Styles */
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
            padding: 20px;
            border-radius: 10px 10px 0 0;
            min-height: 140px;
            display: flex;
            align-items: center;
        }
        .badge-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 6px 12px;
            border-radius: 20px;
        }
        .expand-icon {
            transition: transform 0.3s ease;
            font-size: 1.2rem;
        }
        .expand-icon.rotated {
            transform: rotate(180deg);
        }
        /* Square aspect ratio for course cards */
        .square-card-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 100%; /* 1:1 Aspect Ratio */
        }
        .square-card-content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }
        @media (max-width: 768px) {
            .square-card-wrapper {
                padding-bottom: 0; /* Remove square on mobile */
            }
            .square-card-content {
                position: relative;
            }
        }
        /* Admin Dashboard Cards - Same style as Teacher/Student cards */
        .admin-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: 100%;
        }
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .admin-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            border-radius: 10px 10px 0 0;
            min-height: 90px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .admin-card-header .card-icon {
            font-size: 2rem;
            margin-bottom: 8px;
        }
        .admin-card-header .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
        }
        .admin-card .card-body {
            min-height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 12px;
        }
        .admin-card .card-count {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        .admin-card .btn {
            font-size: 0.85rem;
            padding: 6px 12px;
        }
        /* Users Card - Full width with table */
        .users-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            height: auto;
        }
        .users-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .users-card-header h5 {
            margin: 0;
            font-size: 1.25rem;
        }
        .users-table-container {
            max-height: 600px;
            overflow-y: auto;
            padding: 15px;
        }
        /* Scrollbar styling for users table */
        .users-table-container::-webkit-scrollbar {
            width: 8px;
        }
        .users-table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .users-table-container::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }
        .users-table-container::-webkit-scrollbar-thumb:hover {
            background: #5568d3;
        }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .header-section { padding: 15px; }
        }
    </style>
</head>
<body>
<div class="dashboard-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-<?= $role === 'admin' ? 'user-shield' : ($role === 'teacher' ? 'chalkboard-teacher' : 'graduation-cap') ?> text-primary me-2"></i>
                    <?= ucfirst($role ?? 'User') ?> Dashboard
                </h2>
                <p class="text-muted mb-0">Welcome back<?= isset($name) ? ', ' . esc($name) : '' ?>!</p>
            </div>
            <div class="d-flex gap-2 mt-3 mt-md-0 align-items-center">
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
                        <i class="fas fa-user-circle me-1"></i><?= esc($name ?? 'User') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <?php if ($role === 'admin'): ?>
                        <li>
                            <a class="dropdown-item" href="<?= base_url('admin/aboutUsers') ?>">
                                <i class="fas fa-info-circle me-1"></i>About Users
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
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

    <div class="row g-4">
        <!-- Role-based Widgets -->
        <?php if ($role === 'admin'): ?>
            <!-- Left side: Users Card with full user list displayed -->
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="card users-card">
                    <div class="users-card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>
                                <i class="fa fa-users me-2"></i>Registered Users
                                <span class="badge bg-light text-primary ms-2"><?= $totalUsers ?? 0 ?></span>
                            </h5>
                            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                <i class="fa fa-user-plus me-1"></i>Add User
                            </button>
                        </div>
                    </div>
                    <!-- Users table displayed directly in card (no modal needed) -->
                    <!-- Search and Filter Controls -->
                    <div class="p-3 border-bottom bg-light">
                        <div class="row g-2">
                            <div class="col-md-8">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text">
                                        <i class="fa fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="userSearchInput" placeholder="Search by name or email...">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <select class="form-select form-select-sm" id="roleFilterSelect">
                                    <option value="">All Roles</option>
                                    <option value="admin">Admin</option>
                                    <option value="teacher">Teacher</option>
                                    <option value="student">Student</option>
                                </select>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">
                            <span id="userCountDisplay">Showing <?= count($users ?? []) ?> users</span>
                        </small>
                    </div>
                    <div class="users-table-container">
                        <table class="table table-hover table-sm" id="usersTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): foreach ($users as $user): ?>
                                <tr data-user-id="<?= intval($user['id']) ?>" 
                                    data-user-name="<?= esc(strtolower($user['name'])) ?>" 
                                    data-user-email="<?= esc(strtolower($user['email'])) ?>" 
                                    data-user-role="<?= esc(strtolower($user['role'])) ?>" 
                                    class="user-row">
                                    <td class="user-name"><?= esc($user['name']) ?></td>
                                    <td class="user-email"><?= esc($user['email']) ?></td>
                                    <td class="user-role">
                                        <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : ($user['role'] === 'teacher' ? 'bg-warning text-dark' : 'bg-info') ?>">
                                            <?= esc(ucfirst($user['role'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary me-1 edit-user-btn"
                                            data-user-id="<?= intval($user['id']) ?>"
                                            data-user-name="<?= esc($user['name']) ?>"
                                            data-user-email="<?= esc($user['email']) ?>"
                                            data-user-role="<?= esc($user['role']) ?>">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <?php $isAdmin = (isset($user['role']) && $user['role'] === 'admin'); ?>
                                        <button type="button" class="btn btn-sm <?= $isAdmin ? 'btn-outline-warning' : 'btn-warning' ?> delete-user-btn" 
                                            data-user-id="<?= intval($user['id']) ?>" 
                                            data-user-role="<?= esc($user['role']) ?>" 
                                            <?= $isAdmin ? 'title="Cannot deactivate another admin"' : '' ?>>
                                            <i class="fa fa-user-slash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="4" class="text-muted text-center py-4">No users found.</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Right side: Admin Dashboard Cards -->
            <div class="col-lg-4 col-md-12">
                <div class="row g-3 admin-cards-grid">
                <!-- Edit User Modal -->
                <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <form id="editUserForm" action="<?= base_url('admin/updateUser') ?>" method="post">
                        <input type="hidden" name="id" id="edit-user-id">
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="edit-user-name" required>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="edit-user-email" required>
                          </div>
                          <div class="mb-3" id="edit-user-role-container">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" id="edit-user-role" required>
                              <option value="admin">Admin</option>
                              <option value="teacher">Teacher</option>
                              <option value="student">Student</option>
                            </select>
                            <!-- Hidden field for admin role when role selection is disabled -->
                            <input type="hidden" name="role" id="edit-user-role-hidden" disabled>
                            <div id="edit-user-role-readonly" class="form-control-plaintext fw-bold d-none">Admin</div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Change Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input type="password" class="form-control" name="password" id="edit-user-password" placeholder="New password (optional)">
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" class="btn btn-primary w-100">Save changes</button>
                        </div>
                      </form>
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
                      <form id="addUserForm" action="<?= base_url('admin/addUser') ?>" method="post">
                        <div class="modal-body">
                          <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" name="name" id="add-name" required>
                            <div id="addNameInvalid" class="form-text text-danger small mt-1 d-none">Name may only contain letters, numbers and spaces.</div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" id="add-email" required>
                            <div id="addEmailInvalid" class="form-text text-danger small mt-1 d-none">Invalid email format: only letters, numbers, @, and . are allowed.</div>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" name="role" id="add-role" required>
                              <option value="admin">Admin</option>
                              <option value="teacher">Teacher</option>
                              <option value="student">Student</option>
                            </select>
                          </div>
                          <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" id="add-password" required>
                            <div id="addPasswordInvalid" class="form-text text-danger small mt-1 d-none">Password must be at least 6 characters.</div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <button type="submit" id="addUserSubmit" class="btn btn-success w-100">Add User</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="deleteConfirmModalLabel"><i class="fa fa-user-slash me-2"></i>Confirm Deactivate</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p id="deleteConfirmMessage">Are you sure you want to deactivate this user?</p>
                        <div id="deleteAlertPlaceholder"></div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-warning" id="confirmDeleteBtn"><i class="fa fa-user-slash me-1"></i>Confirm Deactivate</button>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Cannot Deactivate (admin) Modal -->
                <div class="modal fade" id="cannotDeleteModal" tabindex="-1" aria-labelledby="cannotDeleteModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                      <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="cannotDeleteModalLabel"><i class="fa fa-exclamation-triangle me-2"></i>Action Not Allowed</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <p class="mb-0">You cannot deactivate another admin account.</p>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                      </div>
                    </div>
                  </div>
                </div>
                    <!-- Courses Card -->
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="admin-card-header">
                                <div class="card-icon">
                                    <i class="fa fa-book"></i>
                                </div>
                                <div class="card-title">Courses</div>
                            </div>
                            <div class="card-body">
                                <div class="card-count text-secondary"><?= $totalCourses ?? 0 ?></div>
                                <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#manageCoursesModal">
                                    <i class="fas fa-cog me-1"></i>Manage
                                </button>
                            </div>
                        </div>
                    </div>
                
                <!-- Manage Courses Modal -->
                <div class="modal fade" id="manageCoursesModal" tabindex="-1" aria-labelledby="manageCoursesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="manageCoursesModalLabel">
                                    <i class="fas fa-book me-2"></i>Manage Courses
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Toggle Buttons -->
                                <div class="d-flex gap-2 mb-3">
                                    <button class="btn btn-primary flex-fill" type="button" id="toggleCreateCourse">
                                        <i class="fas fa-plus-circle me-1"></i>Create Course
                                    </button>
                                    <button class="btn btn-secondary flex-fill" type="button" id="toggleAllCourses">
                                        <i class="fas fa-list me-1"></i>All Courses
                                    </button>
                                </div>
                                
                                <!-- Create Course Collapsible Panel -->
                                <div class="collapse" id="createCoursePanel">
                                    <div class="card border shadow-sm">
                                        <div class="card-header-gradient">
                                            <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Course</h6>
                                        </div>
                                        <div class="card-body">
                                            <form id="createCourseForm">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="course_code" class="form-label">Course Code *</label>
                                                        <input type="text" class="form-control" id="course_code" name="course_code" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="units" class="form-label">Units</label>
                                                        <input type="number" class="form-control" id="units" name="units" min="1" max="10">
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="course_name" class="form-label">Course Name *</label>
                                                        <input type="text" class="form-control" id="course_name" name="course_name" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="semester" class="form-label">Semester *</label>
                                                        <select class="form-select" id="semester" name="semester" required>
                                                            <option value="">-- Select Semester --</option>
                                                            <option value="1st Semester">1st Semester</option>
                                                            <option value="2nd Semester">2nd Semester</option>
                                                            <option value="Summer">Summer</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <!-- Placeholder for alignment -->
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="description" class="form-label">Description</label>
                                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="fas fa-save me-1"></i>Create Course
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                            <div id="createCourseMessage" class="mt-3"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- All Courses Collapsible Panel -->
                                <div class="collapse" id="allCoursesPanel">
                                    <div class="card border shadow-sm">
                                        <div class="card-header-gradient">
                                            <h6 class="mb-0"><i class="fas fa-list me-2"></i>All Courses</h6>
                                        </div>
                                        <div class="card-body">
                                            <!-- Search and Filter Controls -->
                                            <div class="p-3 border-bottom bg-light mb-3">
                                                <div class="row g-2">
                                                    <div class="col-md-8">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">
                                                                <i class="fa fa-search"></i>
                                                            </span>
                                                            <input type="text" class="form-control" id="courseSearchInput" placeholder="Search by course code or name...">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <select class="form-select form-select-sm" id="semesterFilterSelect">
                                                            <option value="">All Semesters</option>
                                                            <option value="1st Semester">1st Semester</option>
                                                            <option value="2nd Semester">2nd Semester</option>
                                                            <option value="Summer">Summer</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block mt-2">
                                                    <span id="courseCountDisplay">Loading courses...</span>
                                                </small>
                                            </div>
                                            <div id="coursesListContainer">
                                                <div class="text-center">
                                                    <div class="spinner-border spinner-border-sm" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="text-muted mt-2">Loading courses...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assign Teacher Modal -->
                <div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-labelledby="assignTeacherModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="assignTeacherModalLabel">
                                    <i class="fas fa-user-plus me-2"></i>Assign Teacher
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="assignTeacherForm">
                                    <input type="hidden" id="assign_course_id" name="course_id">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Course:</strong></label>
                                        <p id="assign_course_name" class="text-muted"></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="teacher_id" class="form-label">Select Teacher *</label>
                                        <select class="form-select" id="teacher_id" name="teacher_id" required>
                                            <option value="">-- Select Teacher --</option>
                                        </select>
                                    </div>
                                    <div id="assignTeacherMessage"></div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-primary" id="confirmAssignBtn">
                                    <i class="fas fa-check me-1"></i>Assign Teacher
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                    <!-- Enroll Students Card -->
                    <div class="col-12">
                        <div class="card admin-card">
                            <div class="admin-card-header">
                                <div class="card-icon">
                                    <i class="fa fa-user-plus"></i>
                                </div>
                                <div class="card-title">Enroll Students</div>
                            </div>
                            <div class="card-body">
                                <div class="card-count text-info">
                                    <i class="fas fa-users" style="font-size: 1.5rem;"></i>
                                </div>
                                <button class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#enrollStudentsModal">
                                    <i class="fas fa-cog me-1"></i>Manage
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <!-- Enroll Students Modal -->
                <div class="modal fade" id="enrollStudentsModal" tabindex="-1" aria-labelledby="enrollStudentsModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="enrollStudentsModalLabel">
                                    <i class="fas fa-user-plus me-2"></i>Enroll Students
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="enrollStudentForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="student_name" class="form-label">Student Name *</label>
                                                <input type="text" class="form-control" id="student_name" name="student_name" required 
                                                    pattern="[A-Za-z\s]+" title="Only letters and spaces allowed">
                                                <div class="form-text">Letters and spaces only</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="student_email" class="form-label">Student Email *</label>
                                                <input type="email" class="form-control" id="student_email" name="student_email" required>
                                                <div class="form-text">Valid email format required</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="student_password" class="form-label">Password *</label>
                                                <input type="password" class="form-control" id="student_password" name="student_password" required 
                                                    minlength="6" title="Minimum 6 characters">
                                                <div class="form-text">Minimum 6 characters</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <!-- Updated to support multiple course selection -->
                                                <label for="enroll_course_id" class="form-label">Select Courses *</label>
                                                <select class="form-select" id="enroll_course_id" name="course_id[]" multiple required size="5">
                                                    <option value="" disabled>-- Select Courses (Ctrl/Cmd+Click for multiple) --</option>
                                                </select>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle me-1"></i>Hold Ctrl (Windows) or Cmd (Mac) to select multiple courses
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label d-block">Enrollment Type *</label>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="enroll_type" id="auto_enroll" value="auto" checked>
                                            <label class="form-check-label" for="auto_enroll">
                                                <i class="fas fa-check-circle text-success me-1"></i>Auto-Enroll (Immediate)
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="enroll_type" id="assign_only" value="assign">
                                            <label class="form-check-label" for="assign_only">
                                                <i class="fas fa-clock text-warning me-1"></i>Assign Only (Student Requests)
                                            </label>
                                        </div>
                                    </div>
                                    <div class="alert alert-info small mb-0">
                                        <strong>Auto-Enroll:</strong> Student is immediately enrolled and can access course materials.<br>
                                        <strong>Assign Only:</strong> Course appears in student's "Available Courses" for manual enrollment request.<br>
                                        <strong>Multiple Courses:</strong> You can select multiple courses to enroll the student in all of them at once.<br>
                                        <strong>Note:</strong> If student email exists, they will be enrolled. If not, a new student account will be created automatically.
                                    </div>
                                    <div id="enrollStudentMessage" class="mt-3"></div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-info" id="confirmEnrollBtn">
                                    <i class="fas fa-check me-1"></i>Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Course Modal -->
                <div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editCourseModalLabel">
                                    <i class="fas fa-edit me-2"></i>Edit Course
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editCourseForm">
                                    <input type="hidden" id="edit_course_id" name="course_id">
                                    <div class="mb-3">
                                        <label for="edit_course_code" class="form-label">Course Code *</label>
                                        <input type="text" class="form-control" id="edit_course_code" name="course_code" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_course_name" class="form-label">Course Name *</label>
                                        <input type="text" class="form-control" id="edit_course_name" name="course_name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_units" class="form-label">Units</label>
                                        <input type="number" class="form-control" id="edit_units" name="units" min="0" step="1">
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_semester" class="form-label">Semester *</label>
                                        <select class="form-select" id="edit_semester" name="semester" required>
                                            <option value="">-- Select Semester --</option>
                                            <option value="1st Semester">1st Semester</option>
                                            <option value="2nd Semester">2nd Semester</option>
                                            <option value="Summer">Summer</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_description" class="form-label">Description</label>
                                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="edit_instructor_id" class="form-label">Assigned Teacher</label>
                                        <select class="form-select" id="edit_instructor_id" name="instructor_id">
                                            <option value="">-- Select Teacher --</option>
                                        </select>
                                    </div>
                                    <div id="editCourseMessage"></div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="button" class="btn btn-warning" id="confirmEditBtn">
                                    <i class="fas fa-save me-1"></i>Update Course
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
        <?php elseif ($role === 'teacher'): ?>
            <div class="col-lg-12 col-md-12">
                <!-- Teacher Courses Card -->
                <div class="card mb-3 p-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa fa-chalkboard-teacher text-warning me-2"></i>
                        <span>My Courses</span>
                    </div>
                    <ul class="list-unstyled mb-0">
                        <?php if (!empty($courses)): ?>
                            <?php foreach ($courses as $course): ?>
                                <?php
                                    $enrollDate = '';
                                    if (!empty($course['enrollment_date'])) {
                                        $ts = strtotime($course['enrollment_date']);
                                        if ($ts !== false) {
                                            $enrollDate = date('M d, Y', $ts);
                                        }
                                    }
                                ?>
                                <li class="d-flex justify-content-between align-items-center mb-1">
                                    <a href="#" class="view-materials-link" data-course-id="<?= intval($course['id']) ?>" data-course-name="<?= esc($course['name']) ?>">
                                        <?= esc($course['name']) ?>
                                    </a>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if ($enrollDate !== ''): ?>
                                            <span class="badge bg-primary"><?= esc($enrollDate) ?></span>
                                        <?php endif; ?>
                                    </div>
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
            </div> <!-- Close teacher wrapper -->
            <?php elseif ($role === 'student'): ?>
                <!-- Two-Column Layout: Enrolled Courses (Left) & Available Courses (Right) -->
                <div class="row">
                    <!-- Left Column: Enrolled Courses (70%) -->
                    <div class="col-md-8">
                        <div class="mb-4">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-graduation-cap text-success me-2"></i>
                                <h5 class="mb-0">Enrolled Courses</h5>
                            </div>
                            <?php if (!empty($enrolledCourses)): ?>
                                <!-- Accordion wrapper for exclusive collapse behavior -->
                                <div class="row" id="enrolledCoursesAccordion">
                                    <?php foreach ($enrolledCourses as $course): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card course-card" data-course-id="<?= esc($course['id']) ?>">
                                                <div class="course-header">
                                                    <div class="d-flex justify-content-between align-items-start w-100">
                                                        <div class="flex-grow-1">
                                                            <h5 class="mb-1"><?= esc($course['code'] ?? 'N/A') ?></h5>
                                                            <h6 class="fw-normal"><?= esc($course['name']) ?></h6>
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
                                                    
                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                        <div>
                                                            <?php if (!empty($course['instructor_name'])): ?>
                                                                <span class="badge badge-custom">
                                                                    <i class="fas fa-user me-1"></i>
                                                                    <?= esc($course['instructor_name']) ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                        <?php if (!empty($course['semester'])): ?>
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar me-1"></i><?= esc($course['semester']) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                    
                                                    <!-- Action Buttons Collapsible Panel -->
                                                    <!-- data-bs-parent makes this part of accordion (opening one closes others) -->
                                                    <div class="collapse mt-3" id="actions-<?= esc($course['id']) ?>" data-bs-parent="#enrolledCoursesAccordion">
                                                        <div class="d-grid gap-2">
                                                            <button class="btn btn-outline-primary btn-sm" type="button" 
                                                                    data-bs-toggle="modal" data-bs-target="#studentAssignmentsModal"
                                                                    data-course-id="<?= intval($course['id']) ?>" 
                                                                    data-course-name="<?= htmlspecialchars($course['name'], ENT_QUOTES) ?>">
                                                                <i class="fas fa-tasks me-1"></i>Assignments
                                                            </button>
                                                            <button class="btn btn-outline-success btn-sm view-materials-btn" type="button"
                                                                    data-course-id="<?= intval($course['id']) ?>" 
                                                                    data-course-name="<?= esc($course['name']) ?>">
                                                                <i class="fas fa-book me-1"></i>Materials
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Not enrolled in any courses yet.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Column: Available Courses (30%) -->
                    <div class="col-md-4">
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
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <?php if (!empty($course['code'])): ?>
                                                        <span class="badge bg-secondary"><?= esc($course['code']) ?></span>
                                                    <?php endif; ?>
                                                    <?php if (!empty($course['semester'])): ?>
                                                        <span class="badge bg-info text-dark"><?= esc($course['semester']) ?></span>
                                                    <?php endif; ?>
                                                    <?php 
                                                    $status = $course['status'] ?? 'assigned';
                                                    if ($status === 'pending'): ?>
                                                        <span class="badge bg-warning text-dark"><i class="fa fa-clock"></i> Pending Approval</span>
                                                    <?php elseif ($status === 'rejected'): ?>
                                                        <span class="badge bg-danger"><i class="fa fa-times-circle"></i> Request Rejected</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="fw-bold"><?= esc($course['name']) ?></div>
                                                <?php if (!empty($course['description'])): ?>
                                                    <small class="text-muted d-block mt-1"><?= esc($course['description']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <?php if ($status === 'assigned'): ?>
                                                <button class="btn btn-sm btn-outline-warning request-enroll-btn ms-2" data-course-id="<?= $course['id'] ?>" data-course-name="<?= esc($course['name']) ?>">
                                                    <i class="fa fa-paper-plane"></i> Request Enrollment
                                                </button>
                                            <?php elseif ($status === 'pending'): ?>
                                                <button class="btn btn-sm btn-outline-secondary ms-2" disabled>
                                                    <i class="fa fa-clock"></i> Request Sent
                                                </button>
                                            <?php elseif ($status === 'rejected'): ?>
                                                <button class="btn btn-sm btn-outline-warning request-enroll-btn ms-2" data-course-id="<?= $course['id'] ?>" data-course-name="<?= esc($course['name']) ?>">
                                                    <i class="fa fa-redo"></i> Request Again
                                                </button>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-muted">No available courses to enroll.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add modal for course materials -->
<div class="modal fade" id="courseMaterialsModal" tabindex="-1" aria-labelledby="courseMaterialsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="courseMaterialsModalLabel">Course Materials</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="courseMaterialsContent">
          <div class="text-center text-muted">Loading...</div>
        </div>
      </div>
      <div class="modal-footer">
        <?php if (isset($role) && ($role === 'admin' || $role === 'teacher')): ?>
          <!-- Add File button visible to admin/teacher; href set by JS when modal opens -->
          <a id="addFileBtn" class="btn btn-primary" href="#" role="button">
            <i class="fa fa-upload me-1"></i> Add File
          </a>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Configure jQuery AJAX to include CSRF header (CodeIgniter)
$(function(){
  try {
    var csrfHeaderName = '<?= csrf_header() ?>';
    var csrfToken = $('meta[name="' + csrfHeaderName + '"]').attr('content');
    if (csrfHeaderName && csrfToken) {
      $.ajaxSetup({ headers: { [csrfHeaderName]: csrfToken } });
    }
  } catch (e) {
    console.warn('CSRF header setup failed', e);
  }
  
  // Load notifications
  loadNotifications();
  
  // Refresh notifications every 30 seconds
  setInterval(loadNotifications, 30000);
  
  // ========================================
  // Admin Users Search and Filter Functionality
  // ========================================
  <?php if ($role === 'admin'): ?>
  // Live client-side search for users (debounced for performance)
  let searchTimeout;
  $('#userSearchInput').on('keyup', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
      filterUsers();
    }, 300); // 300ms debounce delay
  });
  
  // Role filter dropdown
  $('#roleFilterSelect').on('change', function() {
    filterUsers();
  });
  
  // Client-side filter users based on search and role selection
  function filterUsers() {
    const searchTerm = $('#userSearchInput').val().toLowerCase().trim();
    const selectedRole = $('#roleFilterSelect').val().toLowerCase();
    let visibleCount = 0;
    
    // Loop through all user rows
    $('.user-row').each(function() {
      const $row = $(this);
      const userName = $row.data('user-name') || '';
      const userEmail = $row.data('user-email') || '';
      const userRole = $row.data('user-role') || '';
      
      // Check if row matches search term (name or email)
      const matchesSearch = searchTerm === '' || 
                           userName.includes(searchTerm) || 
                           userEmail.includes(searchTerm);
      
      // Check if row matches selected role
      const matchesRole = selectedRole === '' || userRole === selectedRole;
      
      // Show row only if it matches both search and role filter
      if (matchesSearch && matchesRole) {
        $row.show();
        visibleCount++;
      } else {
        $row.hide();
      }
    });
    
    // Update count display
    $('#userCountDisplay').text('Showing ' + visibleCount + ' of <?= count($users ?? []) ?> users');
    
    // Show "no results" message if no users match
    if (visibleCount === 0) {
      if ($('#noUsersMessage').length === 0) {
        $('#usersTable tbody').append(
          '<tr id=\"noUsersMessage\"><td colspan=\"4\" class=\"text-center text-muted py-4\">' +
          '<i class=\"fa fa-search me-2\"></i>No users found matching your criteria</td></tr>'
        );
      }
    } else {
      $('#noUsersMessage').remove();
    }
  }
  
  // ========================================
  // Server-side AJAX search functionality
  // ========================================
  // Optional: Add a button to trigger server-side search for larger datasets
  // This complements the client-side filtering above
  
  /**
   * Perform server-side AJAX search
   * Use this for database-level searching when dealing with large user datasets
   */
  function serverSearchUsers() {
    const searchQuery = $('#userSearchInput').val().trim();
    const roleFilter = $('#roleFilterSelect').val();
    
    // Show loading indicator
    $('#usersTable tbody').html(
      '<tr><td colspan="4" class="text-center py-4">' +
      '<i class="fa fa-spinner fa-spin me-2"></i>Searching...</td></tr>'
    );
    
    $.ajax({
      url: '<?= base_url('admin/users/search') ?>',
      type: 'GET',
      data: {
        search: searchQuery,
        role: roleFilter
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          // Clear existing rows
          $('#usersTable tbody').empty();
          
          if (response.users && response.users.length > 0) {
            // Rebuild table with search results
            response.users.forEach(function(user) {
              const badgeClass = user.role === 'admin' ? 'bg-danger' : 
                                (user.role === 'teacher' ? 'bg-warning text-dark' : 'bg-info');
              
              const row = `
                <tr data-user-id="${user.id}" 
                    data-user-name="${user.name.toLowerCase()}" 
                    data-user-email="${user.email.toLowerCase()}" 
                    data-user-role="${user.role.toLowerCase()}" 
                    class="user-row">
                  <td class="user-name">${escapeHtml(user.name)}</td>
                  <td class="user-email">${escapeHtml(user.email)}</td>
                  <td class="user-role">
                    <span class="badge ${badgeClass}">${capitalizeFirst(user.role)}</span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary edit-user-btn" 
                            data-id="${user.id}" 
                            data-name="${escapeHtml(user.name)}" 
                            data-email="${escapeHtml(user.email)}" 
                            data-role="${user.role}">
                      <i class="fa fa-edit"></i>
                    </button>
                    ${user.role !== 'admin' ? 
                      `<button class="btn btn-sm btn-outline-danger delete-user-btn" 
                               data-id="${user.id}" 
                               data-name="${escapeHtml(user.name)}">
                        <i class="fa fa-trash"></i>
                      </button>` : ''}
                  </td>
                </tr>
              `;
              $('#usersTable tbody').append(row);
            });
            
            // Update count
            $('#userCountDisplay').text(`Showing ${response.count} users`);
          } else {
            // No results
            $('#usersTable tbody').html(
              '<tr><td colspan="4" class="text-center text-muted py-4">' +
              '<i class="fa fa-search me-2"></i>No users found matching your criteria</td></tr>'
            );
            $('#userCountDisplay').text('Showing 0 users');
          }
        } else {
          alert('Search failed: ' + (response.message || 'Unknown error'));
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX search error:', error);
        $('#usersTable tbody').html(
          '<tr><td colspan="4" class="text-center text-danger py-4">' +
          '<i class="fa fa-exclamation-triangle me-2"></i>Search failed. Please try again.</td></tr>'
        );
      }
    });
  }
  
  // Helper functions for HTML escaping and formatting
  function escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
  }
  
  function capitalizeFirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }
  
  // Optional: Uncomment to enable automatic server-side search on Enter key
  // $('#userSearchInput').on('keypress', function(e) {
  //   if (e.which === 13) { // Enter key
  //     e.preventDefault();
  //     serverSearchUsers();
  //   }
  // });
  
  <?php endif; ?>
  // ========================================
  
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
        
        // Check for enrollment approval/rejection notifications and reload page
        if (response.notifications && response.notifications.length > 0) {
          const enrollmentNotifs = response.notifications.filter(function(notif) {
            return notif.type === 'enrollment_approved' || notif.type === 'enrollment_rejected';
          });
          
          // If there are new enrollment status notifications, reload page to update course lists
          if (enrollmentNotifs.length > 0) {
            const hasUnreadEnrollment = enrollmentNotifs.some(function(notif) {
              return notif.is_read == 0 || notif.is_read === '0';
            });
            
            // Store flag in sessionStorage to prevent infinite reload
            if (hasUnreadEnrollment && !sessionStorage.getItem('enrollment_notif_reload')) {
              sessionStorage.setItem('enrollment_notif_reload', 'true');
              setTimeout(function() {
                location.reload();
              }, 2000);
              return; // Stop processing to avoid display issues
            }
          }
          
          // Check for graded submission notifications and refresh assignment status
          const gradedNotifs = response.notifications.filter(function(notif) {
            return notif.type === 'submission_graded' && (notif.is_read == 0 || notif.is_read === '0');
          });
          
          if (gradedNotifs.length > 0) {
            // If assignments modal is open, reload the current course's assignments
            const assignmentsModal = document.getElementById('studentAssignmentsModal');
            if (assignmentsModal && assignmentsModal.classList.contains('show')) {
              const currentCourseId = $('#studentAssignmentsCourseId').val();
              if (currentCourseId && typeof loadStudentAssignments === 'function') {
                console.log('Reloading assignments due to grade notification');
                loadStudentAssignments(currentCourseId);
              }
            }
          }
        }
        
        // Clear the reload flag after displaying notifications
        sessionStorage.removeItem('enrollment_notif_reload');
        
        // Update notification list
        const notificationList = $('#notificationList');
        if (response.notifications && response.notifications.length > 0) {
          let html = '';
          response.notifications.forEach(function(notif) {
            const isUnread = notif.is_read == 0 || notif.is_read === '0';
            const unreadClass = isUnread ? 'unread bg-light' : '';
            let icon = 'fa-info-circle';
            if (notif.type === 'announcement') icon = 'fa-bullhorn';
            else if (notif.type === 'enrollment_approved') icon = 'fa-check-circle text-success';
            else if (notif.type === 'enrollment_rejected') icon = 'fa-times-circle text-danger';
            else if (notif.type === 'enrollment_request') icon = 'fa-paper-plane text-warning';
            else if (notif.type === 'submission_graded') icon = 'fa-star text-warning';
            else if (notif.type === 'submission_received') icon = 'fa-paper-plane text-info';
            
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

function escapeHtml(text) {
  const div = document.createElement('div');
  div.textContent = text;
  return div.innerHTML;
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
</script>
<script>
$(function() {
    // Listen for request enrollment button click (student dashboard)
    $('.request-enroll-btn').on('click', function(e) {
        e.preventDefault();
        var $btn = $(this);
        var courseId = $btn.data('course-id');
        var courseName = $btn.data('course-name');
        
        // Change button to "Requesting..." state
        var originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Requesting...');
        
        $.post('<?= base_url('course/request-enrollment') ?>', { course_id: courseId }, function(data) {
            // Remove any previous alert
            $('.enroll-alert').remove();
            // Show Bootstrap alert
            var alertType = data.status === 'success' ? 'alert-info' : 'alert-danger';
            var $alert = $('<div class="alert enroll-alert ' + alertType + ' mt-2 alert-dismissible fade show" role="alert">' + 
                data.message + 
                '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
            $btn.closest('.card').prepend($alert);
            
            if (data.status === 'success') {
                // Update the entire list item to show pending state
                var $listItem = $btn.closest('.list-group-item');
                
                // Update badges - add pending badge
                var $badgeContainer = $listItem.find('.d-flex.align-items-center.gap-2').first();
                $badgeContainer.find('.badge.bg-warning, .badge.bg-danger').remove(); // Remove old status badges
                $badgeContainer.append('<span class="badge bg-warning text-dark"><i class="fa fa-clock"></i> Pending Approval</span>');
                
                // Replace button with "Request Sent" button
                $btn.replaceWith('<button class="btn btn-sm btn-outline-secondary ms-2" disabled><i class="fa fa-clock"></i> Request Sent</button>');
                
                // Auto-hide alert after 5 seconds
                setTimeout(function() {
                    $alert.fadeOut(function() { $(this).remove(); });
                }, 5000);
            } else {
                // Re-enable button on error
                $btn.prop('disabled', false).html(originalHtml);
                
                // Auto-hide error alert after 5 seconds
                setTimeout(function() {
                    $alert.fadeOut(function() { $(this).remove(); });
                }, 5000);
            }
        }, 'json').fail(function() {
            // Re-enable button on AJAX error
            $btn.prop('disabled', false).html(originalHtml);
            $('.enroll-alert').remove();
            var $alert = $('<div class="alert enroll-alert alert-danger mt-2" role="alert">Failed to send request. Please try again.</div>');
            $btn.closest('.card').prepend($alert);
        });
    });

    // Course Management for Admin
    <?php if ($role === 'admin'): ?>
    // Load courses and teachers when modal opens
    $('#manageCoursesModal').on('show.bs.modal', function() {
        loadTeachers();
    });

    // Exclusive collapse behavior for Create Course and All Courses panels
    $('#toggleCreateCourse').on('click', function() {
        const createPanel = $('#createCoursePanel');
        const allPanel = $('#allCoursesPanel');
        
        // If Create Course panel is currently hidden, show it and hide All Courses
        if (!createPanel.hasClass('show')) {
            allPanel.collapse('hide');
            createPanel.collapse('show');
        } else {
            // If already shown, toggle it (collapse)
            createPanel.collapse('toggle');
        }
    });

    $('#toggleAllCourses').on('click', function() {
        const createPanel = $('#createCoursePanel');
        const allPanel = $('#allCoursesPanel');
        
        // If All Courses panel is currently hidden, show it and hide Create Course
        if (!allPanel.hasClass('show')) {
            createPanel.collapse('hide');
            allPanel.collapse('show');
            // Load courses when panel opens
            loadCourses();
        } else {
            // If already shown, toggle it (collapse)
            allPanel.collapse('toggle');
        }
    });

    // Create Course Form Submit
    $('#createCourseForm').on('submit', function(e) {
        e.preventDefault();
        const formData = {
            course_code: $('#course_code').val(),
            course_name: $('#course_name').val(),
            description: $('#description').val(),
            units: $('#units').val(),
            semester: $('#semester').val()
        };

        $.ajax({
            url: '<?= base_url('admin/courses/create') ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                const msgEl = $('#createCourseMessage');
                if (response.status === 'success') {
                    msgEl.html('<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' + response.message + '</div>');
                    $('#createCourseForm')[0].reset();
                    loadCourses(); // Reload courses list
                } else {
                    msgEl.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + response.message + '</div>');
                }
                setTimeout(() => msgEl.html(''), 5000);
            },
            error: function() {
                $('#createCourseMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Failed to create course</div>');
            }
        });
    });

    // Assign Teacher button click
    $('#confirmAssignBtn').on('click', function() {
        const courseId = $('#assign_course_id').val();
        const teacherId = $('#teacher_id').val();

        if (!teacherId) {
            $('#assignTeacherMessage').html('<div class="alert alert-warning">Please select a teacher</div>');
            return;
        }

        $.ajax({
            url: '<?= base_url('admin/courses/assign') ?>',
            method: 'POST',
            data: { course_id: courseId, teacher_id: teacherId },
            dataType: 'json',
            success: function(response) {
                const msgEl = $('#assignTeacherMessage');
                if (response.status === 'success') {
                    msgEl.html('<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' + response.message + '</div>');
                    setTimeout(() => {
                        $('#assignTeacherModal').modal('hide');
                        loadCourses(); // Reload courses list
                    }, 1500);
                } else {
                    msgEl.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + response.message + '</div>');
                }
            },
            error: function() {
                $('#assignTeacherMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>Failed to assign teacher</div>');
            }
        });
    });

    // Enroll Students Modal - Load courses when modal opens
    $('#enrollStudentsModal').on('show.bs.modal', function() {
        loadCoursesForEnrollment();
        // Clear form
        $('#enrollStudentForm')[0].reset();
        $('#enrollStudentMessage').html('');
    });

    // Confirm Enroll Button - Updated to handle multiple course selection
    $('#confirmEnrollBtn').on('click', function() {
        const studentName = $('#student_name').val().trim();
        const studentEmail = $('#student_email').val().trim();
        const studentPassword = $('#student_password').val();
        // Get all selected course IDs (multiple selection)
        const selectedCourses = $('#enroll_course_id').val() || [];
        const enrollType = $('input[name="enroll_type"]:checked').val();

        // Client-side validation
        if (!studentName || !studentEmail || !studentPassword || selectedCourses.length === 0) {
            $('#enrollStudentMessage').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Please fill in all required fields and select at least one course</div>');
            return;
        }

        // Validate name (letters and spaces only)
        const namePattern = /^[A-Za-z\s]+$/;
        if (!namePattern.test(studentName)) {
            $('#enrollStudentMessage').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Name can only contain letters and spaces</div>');
            return;
        }

        // Validate email format
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(studentEmail)) {
            $('#enrollStudentMessage').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Please enter a valid email address</div>');
            return;
        }

        // Validate password length
        if (studentPassword.length < 6) {
            $('#enrollStudentMessage').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Password must be at least 6 characters</div>');
            return;
        }

        const endpoint = enrollType === 'auto' ? '<?= base_url('admin/enroll/auto') ?>' : '<?= base_url('admin/enroll/assign') ?>';
        
        // Disable button during submission
        const $btn = $('#confirmEnrollBtn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');

        // Process enrollment for each selected course
        let completedRequests = 0;
        let successCount = 0;
        let failedCourses = [];
        const totalCourses = selectedCourses.length;

        $('#enrollStudentMessage').html('<div class="alert alert-info"><i class="fas fa-spinner fa-spin me-2"></i>Enrolling student in ' + totalCourses + ' course(s)...</div>');

        // Enroll student in each selected course
        selectedCourses.forEach(function(courseId) {
            $.ajax({
                url: endpoint,
                method: 'POST',
                data: { 
                    student_name: studentName,
                    student_email: studentEmail,
                    student_password: studentPassword,
                    course_id: courseId 
                },
                dataType: 'json',
                success: function(response) {
                    completedRequests++;
                    if (response.status === 'success') {
                        successCount++;
                    } else {
                        failedCourses.push(response.message || 'Course ID ' + courseId);
                    }
                    
                    // When all requests are complete, show final result
                    if (completedRequests === totalCourses) {
                        displayEnrollmentResults(successCount, failedCourses, totalCourses);
                        $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i>Submit');
                    }
                },
                error: function(xhr) {
                    completedRequests++;
                    failedCourses.push('Course ID ' + courseId + ' (Server error)');
                    
                    if (completedRequests === totalCourses) {
                        displayEnrollmentResults(successCount, failedCourses, totalCourses);
                        $btn.prop('disabled', false).html('<i class="fas fa-check me-1"></i>Submit');
                    }
                }
            });
        });
    });

    // Display enrollment results after processing all courses
    function displayEnrollmentResults(successCount, failedCourses, totalCourses) {
        const msgEl = $('#enrollStudentMessage');
        
        if (successCount === totalCourses) {
            // All enrollments successful
            msgEl.html('<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Successfully enrolled student in all ' + totalCourses + ' course(s)!</div>');
            $('#enrollStudentForm')[0].reset();
            $('input[name="enroll_type"][value="auto"]').prop('checked', true);
            setTimeout(() => {
                msgEl.html('');
            }, 4000);
        } else if (successCount > 0) {
            // Partial success
            let message = '<div class="alert alert-warning">';
            message += '<i class="fas fa-exclamation-triangle me-2"></i>';
            message += 'Enrolled in ' + successCount + ' of ' + totalCourses + ' courses.<br>';
            message += '<small>Failed: ' + failedCourses.join(', ') + '</small>';
            message += '</div>';
            msgEl.html(message);
        } else {
            // All failed
            let message = '<div class="alert alert-danger">';
            message += '<i class="fas fa-exclamation-circle me-2"></i>Failed to enroll in any courses.<br>';
            message += '<small>Errors: ' + failedCourses.join(', ') + '</small>';
            message += '</div>';
            msgEl.html(message);
        }
    }

    // Load courses for enrollment dropdown (supports multiple selection)
    function loadCoursesForEnrollment() {
        $.ajax({
            url: '<?= base_url('admin/courses/list') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#enroll_course_id');
                    // Clear and populate - no default option needed for multiple select
                    select.html('');
                    response.courses.forEach(function(course) {
                        select.append(`<option value="${course.id}">${escapeHtml(course.course_code)} - ${escapeHtml(course.course_name)}</option>`);
                    });
                }
            },
            error: function() {
                console.error('Failed to load courses');
            }
        });
    }

    $('#confirmEditBtn').on('click', function() {
        const formData = {
            course_id: $('#edit_course_id').val(),
            course_code: $('#edit_course_code').val(),
            course_name: $('#edit_course_name').val(),
            units: $('#edit_units').val(),
            semester: $('#edit_semester').val(),
            description: $('#edit_description').val(),
            instructor_id: $('#edit_instructor_id').val() || null
        };

        if (!formData.course_code || !formData.course_name || !formData.semester) {
            $('#editCourseMessage').html('<div class="alert alert-warning">Please fill in all required fields</div>');
            return;
        }

        $.ajax({
            url: '<?= base_url('admin/courses/update') ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                const msgEl = $('#editCourseMessage');
                if (response.status === 'success') {
                    msgEl.html('<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>' + response.message + '</div>');
                    setTimeout(() => {
                        $('#editCourseModal').modal('hide');
                        loadCourses(); // Reload courses list
                    }, 1500);
                } else {
                    msgEl.html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + response.message + '</div>');
                }
            },
            error: function(xhr) {
                let errorMsg = 'Failed to update course';
                if (xhr.status === 403) {
                    errorMsg = 'Access denied';
                } else if (xhr.status === 404) {
                    errorMsg = 'Course not found';
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error';
                }
                $('#editCourseMessage').html('<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>' + errorMsg + '</div>');
            }
        });
    });

    function loadCourses() {
        $.ajax({
            url: '<?= base_url('admin/courses/list') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    renderCoursesList(response.courses);
                } else {
                    console.error('Load courses error:', response);
                    $('#coursesListContainer').html('<div class="alert alert-danger">Failed to load courses: ' + (response.message || 'Unknown error') + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', xhr, status, error);
                console.error('Response:', xhr.responseText);
                let errorMsg = 'Error loading courses';
                if (xhr.status === 404) {
                    errorMsg = 'Courses endpoint not found (404)';
                } else if (xhr.status === 403) {
                    errorMsg = 'Access denied (403)';
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error (500)';
                }
                $('#coursesListContainer').html('<div class="alert alert-danger">' + errorMsg + '</div>');
            }
        });
    }

    function loadTeachers() {
        $.ajax({
            url: '<?= base_url('admin/teachers/list') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Populate Assign Teacher dropdown
                    const select = $('#teacher_id');
                    select.html('');
                    response.teachers.forEach(function(teacher) {
                        select.append(`<option value="${teacher.id}">${escapeHtml(teacher.name)} (${escapeHtml(teacher.email)})</option>`);
                    });
                    
                    // Populate Edit Course teacher dropdown
                    const editSelect = $('#edit_instructor_id');
                    editSelect.html('<option value="">-- No Teacher (Unassign) --</option>');
                    response.teachers.forEach(function(teacher) {
                        editSelect.append(`<option value="${teacher.id}">${escapeHtml(teacher.name)} (${escapeHtml(teacher.email)})</option>`);
                    });
                }
            }
        });
    }

    // ========================================
    // Course Search and Filter Functionality
    // ========================================
    let courseSearchTimeout;
    let allCoursesData = []; // Store all courses for client-side filtering
    
    // Live client-side search for courses (debounced)
    $('#courseSearchInput').on('keyup', function() {
        clearTimeout(courseSearchTimeout);
        courseSearchTimeout = setTimeout(function() {
            filterCourses();
        }, 300); // 300ms debounce delay
    });
    
    // Semester filter dropdown
    $('#semesterFilterSelect').on('change', function() {
        filterCourses();
    });
    
    // Client-side filter courses based on search and semester selection
    function filterCourses() {
        const searchTerm = $('#courseSearchInput').val().toLowerCase().trim();
        const selectedSemester = $('#semesterFilterSelect').val();
        let visibleCount = 0;
        
        // Filter the courses data
        const filteredCourses = allCoursesData.filter(function(course) {
            const courseCode = (course.course_code || '').toLowerCase();
            const courseName = (course.course_name || '').toLowerCase();
            const courseSemester = course.semester || '';
            
            // Check if matches search term
            const matchesSearch = searchTerm === '' || 
                                 courseCode.includes(searchTerm) || 
                                 courseName.includes(searchTerm);
            
            // Check if matches semester filter
            const matchesSemester = selectedSemester === '' || courseSemester === selectedSemester;
            
            return matchesSearch && matchesSemester;
        });
        
        // Re-render the table with filtered courses
        renderCoursesList(filteredCourses, true);
        
        // Update count display
        $('#courseCountDisplay').text(`Showing ${filteredCourses.length} of ${allCoursesData.length} courses`);
    }
    
    // Optional: Server-side AJAX search (for large datasets)
    function serverSearchCourses() {
        const searchQuery = $('#courseSearchInput').val().trim();
        const semesterFilter = $('#semesterFilterSelect').val();
        
        // Show loading indicator
        $('#coursesListContainer').html(
            '<div class="text-center py-4">' +
            '<div class="spinner-border spinner-border-sm" role="status">' +
            '<span class="visually-hidden">Searching...</span></div>' +
            '<p class="text-muted mt-2">Searching courses...</p></div>'
        );
        
        $.ajax({
            url: '<?= base_url('admin/courses/search') ?>',
            type: 'GET',
            data: {
                search: searchQuery,
                semester: semesterFilter
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    allCoursesData = response.courses; // Update stored data
                    renderCoursesList(response.courses, false);
                    $('#courseCountDisplay').text(`Showing ${response.count} courses`);
                } else {
                    $('#coursesListContainer').html(
                        '<div class="alert alert-danger">' +
                        '<i class="fas fa-exclamation-circle me-2"></i>' + 
                        (response.message || 'Search failed') + '</div>'
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX search error:', error);
                $('#coursesListContainer').html(
                    '<div class="alert alert-danger">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>Search failed. Please try again.</div>'
                );
            }
        });
    }

    function renderCoursesList(courses, isFiltered) {
        if (!courses || courses.length === 0) {
            if (isFiltered) {
                $('#coursesListContainer').html(
                    '<div class="text-muted text-center py-3">' +
                    '<i class="fa fa-search me-2"></i>No courses found matching your criteria</div>'
                );
            } else {
                $('#coursesListContainer').html('<div class="text-muted text-center py-3">No courses created yet</div>');
            }
            return;
        }
        
        // Store all courses data for client-side filtering (only if not already filtered)
        if (!isFiltered) {
            allCoursesData = courses;
            $('#courseCountDisplay').text(`Showing ${courses.length} courses`);
        }

        let html = '<div class="table-responsive"><table class="table table-hover" id="coursesTable"><thead><tr>' +
            '<th>Code</th><th>Name</th><th>Units</th><th>Semester</th><th>Teacher</th><th>Actions</th></tr></thead><tbody>';
        
        courses.forEach(function(course) {
            const teacherName = course.teacher_name || '<span class="text-muted">Not assigned</span>';
            const units = course.units || '-';
            const semester = course.semester || '<span class="text-muted">Not set</span>';
            html += `<tr class="course-row" 
                        data-course-code="${escapeHtml(course.course_code).toLowerCase()}" 
                        data-course-name="${escapeHtml(course.course_name).toLowerCase()}" 
                        data-semester="${escapeHtml(course.semester || '')}">
                <td><strong>${escapeHtml(course.course_code)}</strong></td>
                <td>${escapeHtml(course.course_name)}</td>
                <td>${units}</td>
                <td>${semester}</td>
                <td>${teacherName}</td>
                <td>
                    <button class="btn btn-sm btn-outline-warning edit-course-btn me-1" 
                        data-course-id="${course.id}" 
                        data-course-code="${escapeHtml(course.course_code)}"
                        data-course-name="${escapeHtml(course.course_name)}"
                        data-units="${course.units || ''}"
                        data-semester="${escapeHtml(course.semester || '')}"
                        data-description="${escapeHtml(course.description || '')}"
                        data-instructor-id="${course.instructor_id || ''}">
                        <i class="fas fa-edit me-1"></i>Edit
                    </button>
                    <button class="btn btn-sm btn-outline-primary assign-teacher-btn" 
                        data-course-id="${course.id}" 
                        data-course-name="${escapeHtml(course.course_name)}">
                        <i class="fas fa-user-plus me-1"></i>Assign
                    </button>
                </td>
            </tr>`;
        });
        html += '</tbody></table></div>';
        $('#coursesListContainer').html(html);

        // Attach event handlers to edit buttons
        $('.edit-course-btn').on('click', function() {
            const courseId = $(this).data('course-id');
            const courseCode = $(this).data('course-code');
            const courseName = $(this).data('course-name');
            const units = $(this).data('units');
            const semester = $(this).data('semester');
            const description = $(this).data('description');
            const instructorId = $(this).data('instructor-id');
            
            $('#edit_course_id').val(courseId);
            $('#edit_course_code').val(courseCode);
            $('#edit_course_name').val(courseName);
            $('#edit_units').val(units);
            $('#edit_semester').val(semester);
            $('#edit_description').val(description);
            $('#edit_instructor_id').val(instructorId);
            $('#editCourseMessage').html('');
            $('#editCourseModal').modal('show');
        });

        // Attach event handlers to assign buttons
        $('.assign-teacher-btn').on('click', function() {
            const courseId = $(this).data('course-id');
            const courseName = $(this).data('course-name');
            $('#assign_course_id').val(courseId);
            $('#assign_course_name').text(courseName);
            $('#assignTeacherMessage').html('');
            $('#assignTeacherModal').modal('show');
        });
    }
    <?php endif; ?>
});

document.addEventListener('DOMContentLoaded', function() {
    const baseUrl = '<?= rtrim(base_url(), "/") ?>';
    const modalEl = document.getElementById('courseMaterialsModal');
    const bsModal = new bootstrap.Modal(modalEl);
    const contentEl = document.getElementById('courseMaterialsContent');
    const addFileBtn = document.getElementById('addFileBtn');
    const canUpload = <?= (isset($role) && ($role === 'admin' || $role === 'teacher')) ? 'true' : 'false' ?>;

    function renderMaterialsList(courseName, materials) {
        if (!materials || materials.length === 0) {
            return '<div class="text-muted">No materials uploaded for this course.</div>';
        }
    // helper to escape HTML in strings
    function escapeHtml(str) {
      if (!str) return '';
      return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
    }
        let html = '<div class="list-group">';
        materials.forEach(m => {
            const fname = (m.file_name) ? m.file_name : (m.file_path ? m.file_path.split('/').pop() : 'file');
            html += '<div class="list-group-item d-flex justify-content-between align-items-center">';
            html += '<div><strong>' + escapeHtml(fname) + '</strong>';
            if (m.created_at) {
                html += '<div class="small text-muted">Uploaded: ' + new Date(m.created_at).toLocaleDateString() + '</div>';
            }
            if (m.description) {
                html += '<div class="small text-muted">Note: ' + escapeHtml(m.description) + '</div>';
            }
            html += '</div>';
            html += '<div><a class="btn btn-sm btn-outline-primary" href="' + baseUrl + '/materials/download/' + parseInt(m.id) + '"><i class="fa fa-download"></i> Download</a></div>';
            html += '</div>';
        });
        html += '</div>';
        return html;
    }

    function loadMaterials(courseId, courseName) {
        contentEl.innerHTML = '<div class="text-center py-3"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        // set addFileBtn if applicable
        if (addFileBtn) {
            if (canUpload) {
                addFileBtn.style.display = 'inline-block';
                addFileBtn.setAttribute('href', baseUrl + '/admin/course/' + parseInt(courseId) + '/upload');
            } else {
                addFileBtn.style.display = 'none';
            }
        }
    fetch(baseUrl + '/materials/course/' + parseInt(courseId), {
      method: 'GET',
      credentials: 'same-origin', // include session cookie
      headers: { 'Accept': 'application/json' }
    })
      .then(async r => {
        // If server returned non-JSON (redirect or HTML error), handle gracefully
        const text = await r.text();
        let data = null;
        try {
          data = text ? JSON.parse(text) : null;
        } catch (e) {
          // not JSON
          if (!r.ok) {
            throw new Error('Server returned HTTP ' + r.status + (text ? ': ' + text : ''));
          }
          throw e;
        }

        if (!r.ok) {
          // server returned JSON error message
          const msg = (data && data.message) ? data.message : ('HTTP ' + r.status);
          throw new Error(msg);
        }

        return data;
      })
      .then(data => {
        if (data && data.status === 'success') {
          contentEl.innerHTML = renderMaterialsList(courseName, data.materials);
          modalEl.querySelector('.modal-title').textContent = 'Materials — ' + courseName;
        } else {
          contentEl.innerHTML = '<div class="alert alert-danger">Could not load materials: ' + (data && data.message ? data.message : 'Unknown error') + '</div>';
        }
      }).catch(err => {
        contentEl.innerHTML = '<div class="alert alert-danger">Could not load materials: ' + (err.message || 'Error') + '</div>';
        console.error('Materials fetch error:', err);
      });
    }

    // Delegated click handlers
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.view-materials-btn, .view-materials-link');
        if (!btn) return;
        e.preventDefault();
        const courseId = btn.getAttribute('data-course-id');
        const courseName = btn.getAttribute('data-course-name') || ('Course ' + courseId);
        loadMaterials(courseId, courseName);
        bsModal.show();
    });
});
</script>
<script>
// Chevron rotation for Bootstrap accordion - uses native Bootstrap collapse events
// No manual toggle needed - Bootstrap handles it via data-bs-toggle and data-bs-parent
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
        // DEBUG: Log when panels open/close
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
</script>
<script>
// Student Assignment Functions
$(document).ready(function() {
    var studentBaseUrl = '<?= base_url() ?>';
    console.log('Student assignment script loaded. Base URL:', studentBaseUrl);
    console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
    console.log('jQuery available:', typeof $ !== 'undefined');

    // Listen for modal shown event and load data
    var assignmentsModalEl = document.getElementById('studentAssignmentsModal');
    if (assignmentsModalEl) {
        assignmentsModalEl.addEventListener('show.bs.modal', function (event) {
            console.log('Modal is opening...');
            var button = event.relatedTarget;
            if (button) {
                var courseId = button.getAttribute('data-course-id');
                var courseName = button.getAttribute('data-course-name');
                console.log('Loading assignments for course:', courseId, courseName);
                
                $('#studentAssignmentsModalLabel').text('Assignments - ' + courseName);
                $('#studentAssignmentsCourseId').val(courseId);
                loadStudentAssignments(courseId);
            }
        });
    } else {
        console.error('studentAssignmentsModal element not found!');
    }

    function loadStudentAssignments(courseId) {
        const container = $('#studentAssignmentsListContainer');
        container.html('<div class="text-center py-3"><div class="spinner-border" role="status"></div></div>');
        
        console.log('Loading assignments for course ID:', courseId);
        console.log('AJAX URL:', studentBaseUrl + 'assignments/view/' + courseId);
        
        $.ajax({
            url: studentBaseUrl + 'assignments/view/' + courseId,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('AJAX response:', response);
                if (response.status === 'success') {
                    console.log('Assignments data:', response.assignments);
                    if (response.assignments && response.assignments.length > 0) {
                        renderStudentAssignmentsList(response.assignments);
                    } else {
                        container.html('<div class="alert alert-info">No assignments available for this course yet.</div>');
                    }
                } else {
                    container.html('<div class="alert alert-info">' + (response.message || 'No assignments available.') + '</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Failed to load assignments:', xhr, status, error);
                console.error('Response text:', xhr.responseText);
                container.html('<div class="alert alert-danger">Failed to load assignments. Error: ' + error + '</div>');
            }
        });
    }

    function renderStudentAssignmentsList(assignments) {
        const container = $('#studentAssignmentsListContainer');
        
        console.log('renderStudentAssignmentsList called with:', assignments);
        console.log('Number of assignments:', assignments ? assignments.length : 0);
        
        if (!assignments || assignments.length === 0) {
            container.html('<div class="alert alert-info">No assignments available yet.</div>');
            return;
        }
        
        let html = '<div class="list-group">';
    assignments.forEach(assignment => {
        const dueDate = assignment.due_date ? new Date(assignment.due_date).toLocaleString() : 'No due date';
        const hasFile = assignment.file_name ? true : false;
        const now = new Date();
        const due = assignment.due_date ? new Date(assignment.due_date) : null;
        const isOverdue = due && due < now;
        
        html += `<div class="list-group-item" id="assignment_${assignment.id}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <h6 class="mb-1">${escapeHtml(assignment.title)}</h6>
                    <p class="mb-1 text-muted small">${escapeHtml(assignment.description || 'No description')}</p>
                    <small class="text-muted">
                        <i class="fas fa-calendar-check me-1"></i>Due: ${dueDate}`;
        
        if (isOverdue) {
            html += ` <span class="badge bg-danger ms-2">Overdue</span>`;
        }
        
        html += ` | <i class="fas fa-tasks text-info ms-2 me-1"></i>Total Score: ${assignment.total_score || 'N/A'}
                    </small>`;
        
        if (hasFile) {
            html += `<div class="mt-2">
                <a href="${studentBaseUrl}assignments/download/${assignment.id}" class="btn btn-sm btn-outline-success" target="_blank">
                    <i class="fas fa-download me-1"></i>${escapeHtml(assignment.file_name)}
                </a>
            </div>`;
        }
        
        html += `</div>
                <div id="submitStatus_${assignment.id}">
                    <button class="btn btn-sm btn-primary" onclick="showSubmitForm(${assignment.id})" id="submitBtn_${assignment.id}">
                        <i class="fas fa-paper-plane me-1"></i>Submit
                    </button>
                </div>
            </div>
        </div>`;
    });
    html += '</div>';
    container.html(html);
    
    // Check submission status for each assignment
    assignments.forEach(assignment => {
        checkSubmissionStatus(assignment.id);
    });
}

function checkSubmissionStatus(assignmentId) {
    const currentUserId = <?= session()->get('user_id') ?? session()->get('userID') ?? 0 ?>;
    
    $.ajax({
        url: studentBaseUrl + 'submissions/checkStatus/' + assignmentId,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success' && response.submitted && response.submission) {
                const sub = response.submission;
                const statusContainer = $('#submitStatus_' + assignmentId);
                
                // Format submitted date
                const submittedDate = sub.submitted_at ? new Date(sub.submitted_at).toLocaleDateString('en-US', {
                    month: 'short',
                    day: '2-digit',
                    year: 'numeric'
                }) : '';
                
                let statusHtml = '<div class="text-end">';
                
                // Check if graded
                if (sub.status === 'graded' && sub.score !== null) {
                    statusHtml += `
                        <div class="mb-2">
                            <span class="badge bg-success px-3 py-2">
                                <i class="fas fa-check-circle me-1"></i>Graded
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong class="text-success">Score: ${sub.score} / ${sub.total_score}</strong>
                        </div>
                        <div class="text-muted small mb-2">
                            <i class="fas fa-user-check me-1"></i>Graded by Instructor
                        </div>`;
                } else {
                    // Just submitted, not graded yet
                    statusHtml += `
                        <div class="mb-2">
                            <span class="badge bg-info px-3 py-2">
                                <i class="fas fa-clock me-1"></i>Submitted
                            </span>
                        </div>`;
                }
                
                // Show submission date
                if (submittedDate) {
                    statusHtml += `
                        <div class="text-muted small mb-2">
                            <i class="fas fa-calendar-alt me-1"></i>${submittedDate}
                        </div>`;
                }
                
                // Show student's submitted file download link
                if (sub.file_name && sub.file_path) {
                    statusHtml += `
                        <div class="mt-2">
                            <a href="${studentBaseUrl}submissions/downloadSubmission/${sub.id}" 
                               class="btn btn-sm btn-outline-primary" 
                               target="_blank"
                               title="Download your submission">
                                <i class="fas fa-file-download me-1"></i>Your Submission
                            </a>
                        </div>`;
                }
                
                statusHtml += '</div>';
                statusContainer.html(statusHtml);
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to check submission status:', error);
        }
    });
}

function showSubmitForm(assignmentId) {
    $('#submitAssignmentId').val(assignmentId);
    $('#submitAssignmentForm')[0].reset();
    var assignmentsModal = bootstrap.Modal.getInstance(document.getElementById('studentAssignmentsModal'));
    if (assignmentsModal) assignmentsModal.hide();
    var submitModal = new bootstrap.Modal(document.getElementById('submitAssignmentModal'));
    submitModal.show();
}

function submitAssignment() {
    const assignmentId = $('#submitAssignmentId').val();
    const formData = new FormData($('#submitAssignmentForm')[0]);
    
    // Check if at least one field is filled
    const textSubmission = $('#submissionText').val().trim();
    const fileInput = $('#submissionFile')[0];
    const hasFile = fileInput.files.length > 0;
    
    if (!textSubmission && !hasFile) {
        alert('Please provide either a text submission or upload a file.');
        return;
    }
    
    $.ajax({
        url: studentBaseUrl + 'submissions/submit/' + assignmentId,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                alert('Assignment submitted successfully!');
                var submitModal = bootstrap.Modal.getInstance(document.getElementById('submitAssignmentModal'));
                if (submitModal) submitModal.hide();
                var assignmentsModal = new bootstrap.Modal(document.getElementById('studentAssignmentsModal'));
                assignmentsModal.show();
                loadStudentAssignments($('#studentAssignmentsCourseId').val());
            } else {
                alert('Error: ' + (response.message || 'Failed to submit assignment.'));
            }
        },
        error: function(xhr, status, error) {
            console.error('Failed to submit assignment:', xhr, status, error);
            alert('Failed to submit assignment. Please check the form and try again.');
        }
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

    // Make all functions globally available
    window.loadStudentAssignments = loadStudentAssignments;
    window.renderStudentAssignmentsList = renderStudentAssignmentsList;
    window.checkSubmissionStatus = checkSubmissionStatus;
    window.showSubmitForm = showSubmitForm;
    window.submitAssignment = submitAssignment;
    window.escapeHtml = escapeHtml;
});
</script>
<script>
// Users actions: edit & delete handlers
$(function(){
  // Open Edit modal and populate fields
  $(document).on('click', '.edit-user-btn', function(e){
    e.preventDefault();
    var $btn = $(this);
    var id = $btn.data('user-id');
    var name = $btn.data('user-name');
    var email = $btn.data('user-email');
    var role = $btn.data('user-role');

    $('#edit-user-id').val(id);
    $('#edit-user-name').val(name);
    $('#edit-user-email').val(email);
    
    // Handle role field based on user role
    if (role === 'admin') {
      // Hide the dropdown and show readonly text for admin users
      $('#edit-user-role').addClass('d-none').prop('disabled', true);
      $('#edit-user-role-hidden').prop('disabled', false).val(role);
      $('#edit-user-role-readonly').removeClass('d-none');
    } else {
      // Show the dropdown for non-admin users
      $('#edit-user-role').removeClass('d-none').prop('disabled', false).val(role);
      $('#edit-user-role-hidden').prop('disabled', true).val('');
      $('#edit-user-role-readonly').addClass('d-none');
    }
    
    // clear password field - optional
    $('#edit-user-password').val('');

    var editModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    editModal.show();
  });

  // Reset edit modal when it's hidden
  $('#editUserModal').on('hidden.bs.modal', function() {
    // Reset role field visibility to default state
    $('#edit-user-role').removeClass('d-none').prop('disabled', false);
    $('#edit-user-role-hidden').prop('disabled', true).val('');
    $('#edit-user-role-readonly').addClass('d-none');
    // Clear any alerts
    $('#editUserModal .alert').remove();
  });

  // Submit edit form via AJAX to avoid full page reload
  $('#editUserForm').on('submit', function(e){
    e.preventDefault();
    var form = this;
    var action = $(form).attr('action');
    // clear previous alerts
    $('#editUserModal .alert').remove();

    // CSRF header and token
    var CSRF_HEADER = '<?= csrf_header() ?>';
    var CSRF_TOKEN = $('meta[name="' + CSRF_HEADER + '"]').attr('content') || '';

    var fd = new FormData(form);
    console.debug('Update request ->', action, fd);

    fetch(action, {
      method: 'POST',
      credentials: 'same-origin',
      headers: Object.assign({ 'X-Requested-With': 'XMLHttpRequest' }, CSRF_HEADER && CSRF_TOKEN ? { [CSRF_HEADER]: CSRF_TOKEN } : {}),
      body: fd
    })
    .then(async function(resp){
      console.debug('Update response status', resp.status, resp.statusText);
      var text = await resp.text();
      var data = null;
      try { data = text ? JSON.parse(text) : null; } catch (err) { console.error('Invalid JSON update response', text); throw { status: resp.status, text: text }; }
      var newToken = resp.headers.get(CSRF_HEADER);
      if (newToken) { CSRF_TOKEN = newToken; $('meta[name="' + CSRF_HEADER + '"]').attr('content', CSRF_TOKEN); }
      if (!resp.ok) throw { status: resp.status, body: data };
      return data;
    })
    .then(function(data){
      if (data && data.status === 'success') {
        var id = data.user.id;
        var $row = $('tr[data-user-id="' + id + '"]');
        $row.find('.user-name').text(data.user.name);
        $row.find('.user-email').text(data.user.email);
        $row.find('.user-role').text(data.user.role);
        var $editBtn = $row.find('.edit-user-btn');
        $editBtn.attr('data-user-name', data.user.name).attr('data-user-email', data.user.email).attr('data-user-role', data.user.role);
        var $delBtn = $row.find('.delete-user-btn');
        $delBtn.attr('data-user-role', data.user.role);
        if (data.user.role === 'admin') { $delBtn.removeClass('btn-danger').addClass('btn-outline-danger'); } else { $delBtn.removeClass('btn-outline-danger').addClass('btn-danger'); }
        bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
        var $msg = $('<div class="alert alert-success mt-2" role="alert">' + (data.message || 'User updated') + '</div>');
        $('#usersModal .modal-body').prepend($msg);
        setTimeout(function(){ $msg.fadeOut(300, function(){ $(this).remove(); }); }, 2500);
      } else {
        var msg = (data && data.message) ? data.message : 'Update failed';
        var $err = $('<div class="alert alert-danger mt-2" role="alert">' + msg + '</div>');
        $('#editUserModal .modal-body').prepend($err);
      }
    })
    .catch(function(err){
      console.error('Update request error', err);
      var msg = (err && err.body && err.body.message) ? err.body.message : (err && err.text) ? err.text : 'Request failed.';
      var $err = $('<div class="alert alert-danger mt-2" role="alert">' + msg + ' (status: ' + (err.status || 'n/a') + ')</div>');
      $('#editUserModal .modal-body').prepend($err);
    });
  });

  // Delete flow using modals
  var deleteTargetId = null;
  var deleteTargetRow = null;
  var deleteConfirmModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
  var cannotDeleteModal = new bootstrap.Modal(document.getElementById('cannotDeleteModal'));

  $(document).on('click', '.delete-user-btn', function(e){
    e.preventDefault();
    var $btn = $(this);
    var id = $btn.data('user-id');
    var role = $btn.data('user-role');
    var name = $btn.closest('tr').find('.user-name').text() || '';

    if (role === 'admin') {
      // Show cannot-delete modal
      cannotDeleteModal.show();
      return;
    }

    // Prepare confirmation modal
    deleteTargetId = id;
    deleteTargetRow = $btn.closest('tr');
    $('#deleteConfirmMessage').text('Are you sure you want to deactivate user "' + name + '"? The user will not be able to log in, but all data will be preserved and can be restored by activating the user again from the "About Users" page.');
    $('#deleteAlertPlaceholder').html('');
    deleteConfirmModal.show();
  });

  // Confirm delete action
  $('#confirmDeleteBtn').on('click', function(){
    if (!deleteTargetId) return;
    var $btn = $(this);
    $btn.prop('disabled', true).text('Deactivating...');

    var CSRF_HEADER = '<?= csrf_header() ?>';
    var CSRF_TOKEN = $('meta[name="' + CSRF_HEADER + '"]').attr('content') || '';

    var body = new URLSearchParams();
    body.append('id', deleteTargetId);

    fetch('<?= base_url('admin/deleteUser') ?>', {
      method: 'POST',
      credentials: 'same-origin',
      headers: Object.assign({ 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' }, CSRF_HEADER && CSRF_TOKEN ? { [CSRF_HEADER]: CSRF_TOKEN } : {}),
      body: body.toString()
    })
    .then(async function(resp){
      console.debug('Delete response status', resp.status, resp.statusText);
      var text = await resp.text();
      var data = null;
      try { data = text ? JSON.parse(text) : null; } catch (err) { console.error('Invalid JSON delete response', text); throw { status: resp.status, text: text }; }
      var newToken = resp.headers.get(CSRF_HEADER);
      if (newToken) { $('meta[name="' + CSRF_HEADER + '"]').attr('content', newToken); }
      if (!resp.ok) throw { status: resp.status, body: data };
      return data;
    })
    .then(function(data){
      $btn.prop('disabled', false).text('Confirm Deactivate');
      if (data && data.status === 'success') {
        deleteConfirmModal.hide();
        deleteTargetRow.fadeOut(200, function(){ $(this).remove(); });
        var aboutUsersUrl = '<?= base_url("admin/aboutUsers") ?>';
        var $msg = $('<div class="alert alert-success mt-2" role="alert">' + (data.message || 'User deactivated') + '. The user has been moved to <a href="' + aboutUsersUrl + '" class="alert-link">About Users</a> and can be reactivated from there.</div>');
        $('#usersModal .modal-body').prepend($msg);
        setTimeout(function(){ $msg.fadeOut(300, function(){ $(this).remove(); }); }, 5000);
        deleteTargetId = null; deleteTargetRow = null;
      } else {
        var m = (data && data.message) ? data.message : 'Delete failed';
        $('#deleteAlertPlaceholder').html('<div class="alert alert-danger">' + m + '</div>');
      }
    })
    .catch(function(err){
      console.error('Delete request error', err);
      $btn.prop('disabled', false).text('Confirm Deactivate');
      var msg = (err && err.body && err.body.message) ? err.body.message : (err && err.text) ? err.text : 'Request failed.';
      $('#deleteAlertPlaceholder').html('<div class="alert alert-danger">' + msg + ' (status: ' + (err.status || 'n/a') + ')</div>');
    });
  });
});
</script>
<script>
// Client-side validation for Add User modal (real-time + prevent submit)
(function(){
  var form = document.getElementById('addUserForm');
  if (!form) return;
  var nameInput = document.getElementById('add-name');
  var emailInput = document.getElementById('add-email');
  var passwordInput = document.getElementById('add-password');
  var submitBtn = document.getElementById('addUserSubmit');
  var nameErr = document.getElementById('addNameInvalid');
  var emailErr = document.getElementById('addEmailInvalid');
  var pwdErr = document.getElementById('addPasswordInvalid');

  // Allowed patterns
  var nameRe = /^[A-Za-z0-9 ]+$/; // letters, numbers, spaces
  var emailCharsRe = /^[A-Za-z0-9@.]+$/; // only letters, numbers, @ and .

  function validateName() {
    var v = (nameInput.value || '').trim();
    if (!v || !nameRe.test(v)) { nameErr.classList.remove('d-none'); return false; }
    nameErr.classList.add('d-none'); return true;
  }
  function validateEmailChars() {
    var v = (emailInput.value || '').trim();
    if (!v || !emailCharsRe.test(v)) { emailErr.classList.remove('d-none'); return false; }
    emailErr.classList.add('d-none'); return true;
  }
  function validatePassword() {
    var v = passwordInput.value || '';
    if (!v || v.length < 6) { pwdErr.classList.remove('d-none'); return false; }
    pwdErr.classList.add('d-none'); return true;
  }

  function updateSubmitState(){
    var ok = validateName() && validateEmailChars() && validatePassword();
    submitBtn.disabled = !ok;
    return ok;
  }

  // Live validation
  nameInput.addEventListener('input', updateSubmitState);
  emailInput.addEventListener('input', updateSubmitState);
  passwordInput.addEventListener('input', updateSubmitState);

  // Prevent submit if invalid
  form.addEventListener('submit', function(e){
    if (!updateSubmitState()) { e.preventDefault(); e.stopPropagation(); nameInput.focus(); }
  });

  // When modal opens, reset state
  var addModalEl = document.getElementById('addUserModal');
  if (addModalEl) {
    addModalEl.addEventListener('show.bs.modal', function(){
      // reset values and messages
      form.reset();
      nameErr.classList.add('d-none'); emailErr.classList.add('d-none'); pwdErr.classList.add('d-none');
      submitBtn.disabled = true;
    });
  }
})();
</script>

    </div> <!-- End dashboard-container -->

<!-- Student Assignments Modal -->
<div class="modal fade" id="studentAssignmentsModal" tabindex="-1" aria-labelledby="studentAssignmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentAssignmentsModalLabel">Assignments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="studentAssignmentsCourseId">
                <div id="studentAssignmentsListContainer"></div>
            </div>
        </div>
    </div>
</div>

<!-- Submit Assignment Modal -->
<div class="modal fade" id="submitAssignmentModal" tabindex="-1" aria-labelledby="submitAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="submitAssignmentModalLabel">Submit Assignment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="submitAssignmentForm">
                    <input type="hidden" id="submitAssignmentId">
                    
                    <div class="mb-3">
                        <label for="submissionText" class="form-label">Text Submission</label>
                        <textarea class="form-control" id="submissionText" name="text_submission" rows="6" 
                                  placeholder="Enter your answer or response here..."></textarea>
                        <small class="form-text text-muted">You can provide a text answer, upload a file, or both.</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="submissionFile" class="form-label">Upload File</label>
                        <input type="file" class="form-control" id="submissionFile" name="submission_file"
                               accept=".pdf,.doc,.docx,.ppt,.pptx,.zip,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">Max 10MB. Allowed: PDF, DOC, DOCX, PPT, PPTX, ZIP, JPG, JPEG, PNG</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitAssignment()">Submit Assignment</button>
            </div>
        </div>
    </div>
</div>

</body>
</html>
