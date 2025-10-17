<!DOCTYPE html>
<html>
<head>
    <title>Announcements</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; }
        ul { list-style-type: none; padding: 0; }
        li { margin-bottom: 20px; padding: 10px; border-bottom: 1px solid #ccc; }
        .title { font-weight: bold; font-size: 18px; }
        .content { margin-top: 5px; }
        .date { font-size: 12px; color: #666; margin-top: 5px; }
    </style>
</head>
<body>
    <h1>All Announcements</h1>
    <ul>
        <?php if (!empty($announcements) && is_array($announcements)): ?>
            <?php foreach($announcements as $announcement): ?>
                <li>
                    <div class="title"><?= esc($announcement['title']) ?></div>
                    <div class="content"><?= esc($announcement['content']) ?></div>
                    <div class="date"><?= esc($announcement['created_at']) ?></div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No announcements found.</li>
        <?php endif; ?>
    </ul>
</body>
</html>
