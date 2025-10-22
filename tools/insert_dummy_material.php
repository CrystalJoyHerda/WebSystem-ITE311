<?php
// Quick script to insert a dummy material record for testing
$host = 'localhost';
$db   = 'lms_herda';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->prepare('INSERT INTO materials (course_id, file_name, file_path, created_at) VALUES (?, ?, ?, ?)');
    $courseId = 1;
    $fileName = 'dummy.txt';
    $filePath = 'uploads/materials/1/dummy.txt';
    $created = date('Y-m-d H:i:s');
    $stmt->execute([$courseId, $fileName, $filePath, $created]);
    echo "Inserted material id: " . $pdo->lastInsertId() . PHP_EOL;
} catch (PDOException $e) {
    echo 'DB error: ' . $e->getMessage() . PHP_EOL;
}
