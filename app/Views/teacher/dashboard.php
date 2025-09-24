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
        .list-group-item {
            background: #22303a;
            color: #19d3c5;
            border: none;
            font-weight: 500;
        }
        .list-group-item.bg-light {
            background: #19d3c5 !important;
            color: #222 !important;
        }
        .btn-create {
            background: #19d3c5;
            color: #222;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.6rem 1.2rem;
            font-size: 1rem;
            border: none;
            transition: background 0.2s;
        }
        .btn-create:hover {
            background: #13b3a7;
            color: #fff;
        }
    </style>
</head>
<body>
<?= view('templates/header') ?>
<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0 text-white"><i class="fa fa-book me-2"></i>Courses You Teach</h4>
                    <a href="<?= base_url('teacher/create-course') ?>" class="btn btn-create">
                        <i class="fa fa-plus me-1"></i>Create New Course/Lesson
                    </a>
                </div>
                <ul class="list-group mb-4">
                    <?php if (!empty($courses)): foreach ($courses as $course): ?>
                        <li class="list-group-item bg-light text-dark fw-bold">
                            <i class="fa fa-book me-2 text-info"></i><?= $course['name'] ?>
                        </li>
                    <?php endforeach; else: ?>
                        <li class="list-group-item text-muted">No courses assigned.</li>
                    <?php endif; ?>
                </ul>
                <h4 class="mb-3 text-white"><i class="fa fa-bell me-2"></i>New Assignment Submissions</h4>
                <ul class="list-group">
                    <?php if (!empty($notifications)): foreach ($notifications as $note): ?>
                        <li class="list-group-item bg-light text-dark">
                            <i class="fa fa-file-alt me-2 text-warning"></i><?= $note ?>
                        </li>
                    <?php endforeach; else: ?>
                        <li class="list-group-item text-muted">No new submissions.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>