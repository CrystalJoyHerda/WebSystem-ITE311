<?php
$host = '127.0.0.1';
$user = 'lms';
$pass = 'lms';
$db   = 'lms_herda';
$port = 3306;

$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    echo "Connect failed: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error . "\n";
    exit(1);
}

$accounts = [
    'admin@lms.com' => 'TestPass123!',
    'teacher1@lms.com' => 'TestPass123!',
    'student1@lms.com' => 'TestPass123!'
];

foreach ($accounts as $email => $plain) {
    $hash = password_hash($plain, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE email = ?");
    $stmt->bind_param('ss', $hash, $email);
    if ($stmt->execute()) {
        echo "Updated password for {$email}\n";
    } else {
        echo "Failed to update {$email}: " . $stmt->error . "\n";
    }
    $stmt->close();
}
$mysqli->close();
