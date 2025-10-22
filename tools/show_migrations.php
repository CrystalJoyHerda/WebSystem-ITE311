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
    $stmt = $pdo->query('SELECT * FROM migrations');
    $rows = $stmt->fetchAll();
    foreach ($rows as $r) {
        echo json_encode($r) . PHP_EOL;
    }
} catch (PDOException $e) {
    echo 'DB error: ' . $e->getMessage() . PHP_EOL;
}
