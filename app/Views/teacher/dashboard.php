<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f2027, #2c5364);
        }
        .sidebar {
            min-height: 100vh;
            background: #222c32;
            color: #fff;
            display: flex;
            flex-direction: column;
            border-radius: 0 20px 20px 0;
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.15);
        }
        .sidebar .nav-link {
            color: #19d3c5;
            font-weight: 500;
            border-radius: 10px;
            margin-bottom: 8px;
            transition: background 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: #19d3c5;
            color: #222c32;
        }
        .sidebar .logout-btn {
            width: 100%;
            border-radius: 10px;
        }
        .dashboard-header {
            background: #19d3c5;
            color: #222c32;
            border-radius: 20px;
            padding: 1.2rem 1rem 1rem 1rem;
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            letter-spacing: 2px;
            margin-bottom: 2rem;
        }
        .card {
            border-radius: 20px;
            box-shadow: 0 4px 16px 0 rgba(31,38,135,0.15);
        }
        .list-group-item {
            border-radius: 10px;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block sidebar py-4">
    <div class="d-flex flex-column h-100 justify-content-between">
        <div>
            <div class="mb-4 d-block text-center">
                <span class="small">
                    <strong><?= session()->get('name') ?></strong> (<?= session()->get('role') ?>)
                </span>
            </div>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                        Dashboard
                    </a>
                </li>
                <?php if (session()->get('role') === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/manage-users') ?>">Manage Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/manage-courses') ?>">Manage Courses</a>
                    </li>
                <?php elseif (session()->get('role') === 'teacher'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/create-course') ?>">Create Course</a>
                    </li>
                <?php elseif (session()->get('role') === 'student'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/courses') ?>">My Courses</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="px-3 pb-3">
            <a href="<?= base_url('logout') ?>" class="btn btn-danger logout-btn w-100">Logout</a>
        </div>
    </div>
</nav>
        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-4">
            <div class="container mt-4">
                <div class="dashboard-header">TEACHER DASHBOARD</div>
                <div class="card p-4 mb-4">
                    <h4 class="mb-3"><i class="fa fa-chalkboard-teacher me-2"></i>Courses You Teach</h4>
                    <ul class="list-group mb-4">
                        <?php if (!empty($courses)): foreach ($courses as $course): ?>
                            <li class="list-group-item bg-light text-dark fw-bold"><i class="fa fa-book me-2 text-info"></i><?= $course['name'] ?></li>
                        <?php endforeach; else: ?>
                            <li class="list-group-item text-muted">No courses assigned.</li>
                        <?php endif; ?>
                    </ul>
                    <h4 class="mb-3"><i class="fa fa-bell me-2"></i>New Assignment Submissions</h4>
                    <ul class="list-group">
                        <?php if (!empty($notifications)): foreach ($notifications as $note): ?>
                            <li class="list-group-item bg-light text-dark"><i class="fa fa-file-alt me-2 text-warning"></i><?= $note ?></li>
                        <?php endforeach; else: ?>
                            <li class="list-group-item text-muted">No new submissions.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>