<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm sticky-top" style="font-size:1.15rem; z-index: 1030;">
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

          <!-- Announcements button to the right of Courses (admin only) -->
          <li class="nav-item">
            <a href="<?= base_url('announcement') ?>" class="btn btn-sm btn-outline-primary ms-2" style="font-size:1.05rem;">
              <i class="fa fa-bullhorn me-1"></i>Announcements
            </a>
          </li>

        <?php elseif ($role === 'teacher'): ?>
          <li class="nav-item">
            <a class="nav-link<?= service('uri')->getSegment(2) === 'courses' ? ' active' : '' ?>" href="<?= base_url('teacher/courses') ?>" style="font-size:1.15rem;">
              <i class="fa fa-book me-1"></i>My Courses
            </a>
          </li>

          <!-- Announcements button to the right of Courses (teacher only) -->
          <li class="nav-item">
            <a href="<?= base_url('announcement') ?>" class="btn btn-sm btn-outline-primary ms-2" style="font-size:1.05rem;">
              <i class="fa fa-bullhorn me-1"></i>Announcements
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
          <!-- Notification Bell -->
          <li class="nav-item dropdown me-3">
            <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size:1.3rem;">
              <i class="fa fa-bell"></i>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationBadge" style="display: none; font-size: 0.7rem;">
                0
              </span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
              <li class="dropdown-header d-flex justify-content-between align-items-center">
                <span><strong>Notifications</strong></span>
                <button class="btn btn-sm btn-link text-decoration-none" id="markAllRead" style="font-size: 0.85rem;">Mark all as read</button>
              </li>
              <li><hr class="dropdown-divider"></li>
              <div id="notificationList">
                <li class="dropdown-item-text text-center text-muted py-3">
                  <i class="fa fa-spinner fa-spin"></i> Loading...
                </li>
              </div>
            </ul>
          </li>

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
              <?php if ($role === 'admin'): ?>
              <li>
                <a class="dropdown-item" href="<?= base_url('admin/aboutUsers') ?>">
                  <i class="fa fa-info-circle me-1"></i>About Users
                </a>
              </li>
              <?php endif; ?>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const baseUrl = '<?= rtrim(base_url(), "/") ?>';
    
    // Function to load notifications
    function loadNotifications() {
        $.ajax({
            url: baseUrl + '/notifications',
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
                    notificationList.empty();
                    
                    if (response.notifications.length === 0) {
                        notificationList.html('<li class="dropdown-item-text text-center text-muted py-3">No notifications</li>');
                    } else {
                        response.notifications.forEach(function(notification) {
                            const isUnread = notification.is_read == 0;
                            const bgClass = isUnread ? 'bg-light' : '';
                            const date = new Date(notification.created_at).toLocaleDateString();
                            
                            const html = `
                                <li class="dropdown-item ${bgClass} border-bottom" style="white-space: normal;">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <p class="mb-1 ${isUnread ? 'fw-bold' : ''}">${escapeHtml(notification.message)}</p>
                                            <small class="text-muted">${date}</small>
                                        </div>
                                        ${isUnread ? `<button class="btn btn-sm btn-link text-primary mark-read-btn p-0 ms-2" data-id="${notification.id}" title="Mark as read"><i class="fa fa-check"></i></button>` : ''}
                                    </div>
                                </li>
                            `;
                            notificationList.append(html);
                        });
                    }
                } else {
                    console.error('Failed to load notifications:', response.message);
                }
            },
            error: function(xhr) {
                console.error('Error loading notifications:', xhr.statusText);
                $('#notificationList').html('<li class="dropdown-item-text text-center text-danger py-3">Error loading notifications</li>');
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
                url: baseUrl + '/notifications/mark_all_read',
                method: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Hide badge
                        badge.hide();
                        // Reload notifications to update the UI
                        loadNotifications();
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
            url: baseUrl + '/notifications/mark_all_read',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Hide badge
                    $('#notificationBadge').hide();
                    // Reload notifications to update UI
                    loadNotifications();
                }
            },
            error: function(xhr) {
                console.error('Failed to mark all as read:', xhr.statusText);
            }
        });
    });
    
    // Function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Mark single notification as read
    $(document).on('click', '.mark-read-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const notificationId = $(this).data('id');
        
        $.ajax({
            url: baseUrl + '/notifications/mark_read/' + notificationId,
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    loadNotifications(); // Reload notifications
                }
            },
            error: function(xhr) {
                console.error('Error marking notification as read:', xhr.statusText);
            }
        });
    });
    
    // Mark all as read
    $('#markAllRead').on('click', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: baseUrl + '/notifications/mark_all_read',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    loadNotifications(); // Reload notifications
                }
            },
            error: function(xhr) {
                console.error('Error marking all as read:', xhr.statusText);
            }
        });
    });
    
    // Load notifications on page load (only if user is logged in)
    <?php if ($role): ?>
    loadNotifications();
    
    // Auto-refresh notifications every 60 seconds
    setInterval(loadNotifications, 60000);
    <?php endif; ?>
});
</script>