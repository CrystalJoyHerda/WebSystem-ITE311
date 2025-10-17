
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Upload Course Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow-sm p-4">
        <h4 class="mb-4">Upload Material for Course #<?= esc($course_id) ?></h4>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (isset($validation)): ?>
            <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
        <?php endif; ?>

        <form method="post" action="<?= current_url() ?>" enctype="multipart/form-data" class="row g-3">
            <?= csrf_field() ?>

            <div class="col-12">
                <label for="material_file" class="form-label">Select File</label>
                <input class="form-control" type="file" name="material_file" id="material_file" required>
                <div class="form-text">Allowed: pdf, doc, docx, ppt, pptx, zip, rar, txt — Max 5MB.</div>
            </div>

            <div class="col-md-8">
                <label for="description" class="form-label">Optional description</label>
                <input type="text" name="description" id="description" class="form-control" value="<?= esc(old('description')) ?>" placeholder="Brief note about this material">
            </div>

            <div class="col-md-4 d-flex align-items-end justify-content-end gap-2">
                <a href="<?= base_url('/admin/courses') ?>" class="btn btn-secondary">Back to Courses</a>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
