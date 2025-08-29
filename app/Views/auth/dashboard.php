<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Welcome to Dashboard</h4>
                        <a href="<?= base_url('logout') ?>" class="btn btn-danger">Logout</a>
                    </div>
                    <div class="card-body">
                        <h5>Hello, <?= session()->get('name') ?>!</h5>
                        <p><strong>Email:</strong> <?= session()->get('email') ?></p>
                        <p><strong>Role:</strong> <?= session()->get('role') ?></p>
                        <p><strong>User ID:</strong> <?= session()->get('userID') ?></p>
                        <hr>
                        <p class="text-success">You are successfully logged in!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>