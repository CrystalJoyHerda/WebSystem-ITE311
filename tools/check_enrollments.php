<?php
$host = 'localhost';
$db   = 'lms_herda';
$user = 'lms';
$pass = 'lms';
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    $stmt = $pdo->query('DESCRIBE enrollments');
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        echo $r['Field'] . ' - ' . $r['Type'] . ' - Null:' . $r['Null'] . ' - Key:' . $r['Key'] . ' - Default:' . ($r['Default'] ?? 'NULL') . PHP_EOL;
    }
} catch (PDOException $e) {
    echo 'DB error: ' . $e->getMessage() . PHP_EOL;
}
