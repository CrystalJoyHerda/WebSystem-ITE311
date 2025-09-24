<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
        .custom-list-group {
            background: transparent;
            border: none;
            padding-left: 0;
        }
        .custom-list-group-item {
            background: #22303a;
            color: #bfc9d4;
            border: none;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
        }
        .custom-list-group-item strong {
            color: #fff;
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
        }
        .stat-card .card-title {
            font-size: 1.2rem;
            font-weight: 600;
        }
        .stat-card .card-text {
            font-size: 2.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?= view('templates/header') ?>
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-lg-8 col-md-10 p-4">
            <div class="section-title mb-2">
                <i class="fa fa-history me-2"></i>Recent Activity
            </div>
            <div class="custom-list-group">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($recentActivity)): foreach ($recentActivity as $activity): ?>
                            <tr>
                                <td><?= $activity['date'] ?></td>
                                <td><?= $activity['user'] ?></td>
                                <td><?= $activity['action'] ?></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr>
                                <td colspan="3" class="custom-list-group-item text-muted">No recent activity.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3 d-flex flex-column align-items-center" style="margin-top: 60px;">
            <a href="<?= base_url('admin/users') ?>" class="text-decoration-none w-100">
                <div class="stat-card text-center py-4 mb-4" style="background: #19d3c5; border-radius: 20px; box-shadow: 0 4px 16px rgba(31,38,135,0.10); color: #fff; width: 100%; transition: box-shadow 0.2s;">
                    <div class="mb-2">
                        <i class="fa fa-users fa-2x"></i>
                        <span class="fs-5 fw-bold ms-2">Total Users</span>
                    </div>
                    <div class="fs-1 fw-bold"><?= $totalUsers ?? 0 ?></div>
                </div>
            </a>
            <a href="<?= base_url('admin/courses') ?>" class="text-decoration-none w-100">
                <div class="stat-card text-center py-4" style="background: #13b3a7; border-radius: 20px; box-shadow: 0 4px 16px rgba(31,38,135,0.10); color: #fff; width: 100%; transition: box-shadow 0.2s;">
                    <div class="mb-2">
                        <i class="fa fa-book fa-2x"></i>
                        <span class="fs-5 fw-bold ms-2">Total Courses</span>
                    </div>
                    <div class="fs-1 fw-bold"><?= $totalCourses ?? 0 ?></div>
                </div>
            </a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>