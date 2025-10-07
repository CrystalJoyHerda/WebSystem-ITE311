<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm mb-4" style="font-size:1.15rem;">
  <div class="container">
    <?php
      $role = session('role');
      $name = esc(session('name') ?? session('email') ?? 'User');
      if ($role === 'admin') {
          $dashboardUrl = base_url('admin/dashboard');
          $dashboardTitle = 'Admin Dashboard';
      } elseif ($role === 'teacher') {
          $dashboardUrl = base_url('teacher/dashboard');
          $dashboardTitle = 'Teacher Dashboard';
      } elseif ($role === 'student') {
          $dashboardUrl = base_url('student/dashboard');
          $dashboardTitle = 'Student Dashboard';
      } else {
          $dashboardUrl = base_url('/');
          $dashboardTitle = 'Dashboard';
      }
    ?>
    <a class="navbar-brand d-flex align-items-center fw-bold me-2" href="<?= $dashboardUrl ?>">
      <i class="fa fa-leaf text-success" style="font-size:1.5rem;"></i>
    </a>
    <span class="fw-semibold d-none d-lg-inline me-4" style="min-width:160px; font-size:1.2rem;"><?= $dashboardTitle ?></span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto align-items-center">
        <li class="nav-item">
          <a class="nav-link<?= service('uri')->getSegment(2) === 'dashboard' ? ' active' : '' ?>" href="<?= $dashboardUrl ?>" style="font-size:1.15rem;">
            <i class="fa fa-home me-1"></i>Dashboard
          </a>
        </li>
        <?php if ($role === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link<?= service('uri')->getSegment(2) === 'users' ? ' active' : '' ?>" href="<?= base_url('admin/users') ?>" style="font-size:1.15rem;">
              <i class="fa fa-users me-1"></i>Users
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link<?= service('uri')->getSegment(2) === 'courses' ? ' active' : '' ?>" href="<?= base_url('admin/courses') ?>" style="font-size:1.15rem;">
              <i class="fa fa-book me-1"></i>Courses
            </a>
          </li>
        <?php elseif ($role === 'teacher'): ?>
          <li class="nav-item">
            <a class="nav-link<?= service('uri')->getSegment(2) === 'courses' ? ' active' : '' ?>" href="<?= base_url('teacher/courses') ?>" style="font-size:1.15rem;">
              <i class="fa fa-book me-1"></i>My Courses
            </a>
          </li>
        <?php elseif ($role === 'student'): ?>
          <li class="nav-item">
            <a class="nav-link<?= service('uri')->getSegment(2) === 'courses' ? ' active' : '' ?>" href="<?= base_url('student/courses') ?>" style="font-size:1.15rem;">
              <i class="fa fa-book me-1"></i>Enrolled Courses
            </a>
          </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if ($role): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" style="font-size:1.15rem;">
              <i class="fa fa-user-circle me-1"></i><?= $name ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li>
                <span class="dropdown-item-text">
                  <strong>Role:</strong> <?= esc(ucfirst($role)) ?>
                </span>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
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