<?php if (! isset($inactiveUsers)) $inactiveUsers = []; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Users - LMS</title>
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
        .page-container {
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
        .card-header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px 10px 0 0;
            border: none;
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
        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
        }
        .btn-outline-secondary:hover {
            background: #6c757d;
            color: white;
        }
        .table thead th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
        }
        .table tbody tr:hover {
            background: #f8f9fa;
        }
        .table tbody td {
            padding: 15px;
            vertical-align: middle;
        }
        @media (max-width: 768px) {
            body { padding: 10px; }
            .header-section { padding: 15px; }
        }
    </style>
</head>
<body>
<div class="page-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="mb-1">
                    <i class="fas fa-users text-primary me-2"></i>About Users - Inactive Accounts
                </h2>
                <p class="text-muted mb-0">Manage inactive user accounts</p>
            </div>
            <div class="mt-3 mt-md-0">
                <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
    </div>

    <!-- Content Section -->
    <div class="card">
        <div class="card-header-gradient">
            <h4 class="mb-0">
                <i class="fas fa-user-slash me-2"></i>Inactive Users
            </h4>
        </div>
        <div class="card-body p-4">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-1"></i>Name</th>
                            <th><i class="fas fa-envelope me-1"></i>Email</th>
                            <th><i class="fas fa-user-tag me-1"></i>Role</th>
                            <th><i class="fas fa-cog me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (! empty($inactiveUsers)): foreach ($inactiveUsers as $u): ?>
                            <tr data-user-id="<?= intval($u['id']) ?>">
                                <td><strong><?= esc($u['name']) ?></strong></td>
                                <td><?= esc($u['email']) ?></td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?= esc(ucfirst($u['role'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success activate-user-btn" data-user-id="<?= intval($u['id']) ?>">
                                        <i class="fas fa-check me-1"></i>Activate
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="4" class="text-muted text-center py-4">
                                    <i class="fas fa-info-circle me-2"></i>No inactive users found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(function(){
  var CSRF_HEADER = '<?= csrf_header() ?>';
  var CSRF_TOKEN = $('meta[name="' + CSRF_HEADER + '"]').attr('content') || '';
  $(document).on('click', '.activate-user-btn', function(e){
    e.preventDefault();
    var $btn = $(this);
    var id = $btn.data('user-id');
    $btn.prop('disabled', true).text('Activating...');
    $.ajax({
      url: '<?= base_url('admin/activateUser') ?>',
      method: 'POST',
      data: { id: id },
      headers: CSRF_HEADER && CSRF_TOKEN ? { [CSRF_HEADER]: CSRF_TOKEN } : {},
      dataType: 'json'
    }).done(function(resp){
      if (resp && resp.status === 'success') {
        $btn.closest('tr').fadeOut(200, function(){ $(this).remove(); });
      } else {
        alert(resp.message || 'Activation failed');
        $btn.prop('disabled', false).text('Activate');
      }
    }).fail(function(){
      alert('Activate request failed');
      $btn.prop('disabled', false).text('Activate');
    });
  });
});
</script>
</body>
</html>
