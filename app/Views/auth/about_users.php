<?php if (! isset($inactiveUsers)) $inactiveUsers = []; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Users</title>
    <?= csrf_meta() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      body { background: #f8f9fa; }
    </style>
</head>
<body>
<?= view('templates/header') ?>

<div class="container" style="padding-top:calc(70px + 1rem);">
  <div class="row">
    <div class="col-md-10 offset-md-1 py-4">
      <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">About Users — Inactive</h5>
          <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (! empty($inactiveUsers)): foreach ($inactiveUsers as $u): ?>
                <tr data-user-id="<?= intval($u['id']) ?>">
                  <td><?= esc($u['name']) ?></td>
                  <td><?= esc($u['email']) ?></td>
                  <td><?= esc($u['role']) ?></td>
                  <td>
                    <button class="btn btn-sm btn-success activate-user-btn" data-user-id="<?= intval($u['id']) ?>">Activate</button>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-muted text-center">No inactive users.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
<?php if (! isset($inactiveUsers)) $inactiveUsers = []; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Users</title>
    <?= csrf_meta() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      body { background: #f8f9fa; }
    </style>
</head>
<body>
<?= view('templates/header') ?>

<div class="container" style="padding-top:calc(70px + 1rem);">
  <div class="row">
    <div class="col-md-10 offset-md-1 py-4">
      <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">About Users — Inactive</h5>
          <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (! empty($inactiveUsers)): foreach ($inactiveUsers as $u): ?>
                <tr data-user-id="<?= intval($u['id']) ?>">
                  <td><?= esc($u['name']) ?></td>
                  <td><?= esc($u['email']) ?></td>
                  <td><?= esc($u['role']) ?></td>
                  <td>
                    <button class="btn btn-sm btn-success activate-user-btn" data-user-id="<?= intval($u['id']) ?>">Activate</button>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-muted text-center">No inactive users.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
<?php if (! isset($inactiveUsers)) $inactiveUsers = []; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Users</title>
    <?= csrf_meta() ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
      body { background: #f8f9fa; }
    </style>
</head>
<body>
<?= view('templates/header') ?>

<div class="container" style="padding-top:calc(70px + 1rem);">
  <div class="row">
    <div class="col-md-10 offset-md-1 py-4">
      <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">About Users — Inactive</h5>
          <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (! empty($inactiveUsers)): foreach ($inactiveUsers as $u): ?>
                <tr data-user-id="<?= intval($u['id']) ?>">
                  <td><?= esc($u['name']) ?></td>
                  <td><?= esc($u['email']) ?></td>
                  <td><?= esc($u['role']) ?></td>
                  <td>
                    <button class="btn btn-sm btn-success activate-user-btn" data-user-id="<?= intval($u['id']) ?>">Activate</button>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-muted text-center">No inactive users.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
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
  <div class="row">
    <div class="col-md-10 offset-md-1 py-4">
      <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="mb-0">About Users — Inactive</h5>
          <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-sm btn-outline-secondary">Back</a>
        </div>
        <div class="table-responsive">
          <table class="table table-sm">
            <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php if (! empty($inactiveUsers)): foreach ($inactiveUsers as $u): ?>
                <tr data-user-id="<?= intval($u['id']) ?>">
                  <td><?= esc($u['name']) ?></td>
                  <td><?= esc($u['email']) ?></td>
                  <td><?= esc($u['role']) ?></td>
                  <td>
                    <button class="btn btn-sm btn-success activate-user-btn" data-user-id="<?= intval($u['id']) ?>">Activate</button>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-muted text-center">No inactive users.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        // remove row from inactive list
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
