<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
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
            border-radius: 10px;
            margin-bottom: 8px;
        }
        .list-group-item.bg-light {
            background: #19d3c5 !important;
            color: #222 !important;
        }
        h4 {
            color: #fff;
        }
    </style>
</head>
<body>
<?= view('templates/header') ?>
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-lg-8 col-md-10 p-4">
            <h4 class="mb-3"><i class="fa fa-book-reader me-2"></i>Your Enrolled Courses</h4>
            <ul class="list-group mb-4">
                <?php if (!empty($courses)): foreach ($courses as $course): ?>
                    <li class="list-group-item bg-light text-dark fw-bold"><i class="fa fa-book me-2 text-info"></i><?= $course['name'] ?></li>
                <?php endforeach; else: ?>
                    <li class="list-group-item text-muted">You are not enrolled in any courses.</li>
                <?php endif; ?>
            </ul>
            <h4 class="mb-3"><i class="fa fa-calendar-alt me-2"></i>Upcoming Deadlines</h4>
            <ul class="list-group mb-4">
                <?php if (!empty($deadlines)): foreach ($deadlines as $deadline): ?>
                    <li class="list-group-item bg-light text-dark"><i class="fa fa-clock me-2 text-warning"></i><?= $deadline ?></li>
                <?php endforeach; else: ?>
                    <li class="list-group-item text-muted">No upcoming deadlines.</li>
                <?php endif; ?>
            </ul>
            <h4 class="mb-3"><i class="fa fa-graduation-cap me-2"></i>Recent Grades/Feedback</h4>
            <ul class="list-group">
                <?php if (!empty($grades)): foreach ($grades as $grade): ?>
                    <li class="list-group-item bg-light text-dark"><i class="fa fa-star me-2 text-success"></i><?= $grade ?></li>
                <?php endforeach; else: ?>
                    <li class="list-group-item text-muted">No recent grades or feedback.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>