<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, #19d3c5 60%, #0f2027 100%);">
    <div class="container-fluid">
        <?php
            $role = session('role');
            if ($role === 'admin') {
                $dashboardName = 'Admin Dashboard';
                $dashboardUrl = base_url('admin/dashboard');
            } elseif ($role === 'teacher') {
                $dashboardName = 'Teacher Dashboard';
                $dashboardUrl = base_url('teacher/dashboard');
            } elseif ($role === 'student') {
                $dashboardName = 'Student Dashboard';
                $dashboardUrl = base_url('student/dashboard');
            } else {
                $dashboardName = 'Welcome';
            }
        ?>
        <a class="navbar-brand fw-bold" href="<?= $dashboardUrl ?>">
            <i class="fa fa-users me-2"></i><?= $dashboardName ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if ($role === 'Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin/users') ?>">User Management</a>
                    </li>
                <?php elseif ($role === 'Teacher'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('teacher/users') ?>">User Management</a>
                    </li>
                <?php elseif ($role === 'Student'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/dashboard') ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('student/users') ?>">User Management</a>
                    </li>
                <?php endif; ?>
                <?php if ($role): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa fa-user-circle me-1"></i>
                            <?= esc(session('name') ?? session('email') ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <span class="dropdown-item-text">
                                    <strong>Role:</strong> <?= esc($role) ?>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('logout') ?>">
                                    <i class="fa fa-sign-out-alt me-1"></i>Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>