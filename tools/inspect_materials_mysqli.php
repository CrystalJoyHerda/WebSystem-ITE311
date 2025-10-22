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

echo "Connected to {$db} as {$user}@{$host}:{$port}\n";

// Check table exists
$res = $mysqli->query("SHOW TABLES LIKE 'materials'");
if (!$res) {
    echo "SHOW TABLES query failed: " . $mysqli->error . "\n";
    exit(1);
}
if ($res->num_rows === 0) {
    echo "Table 'materials' does NOT exist in database {$db}.\n";
    exit(0);
}

echo "Table 'materials' exists.\n\n";

// Describe
if ($desc = $mysqli->query("DESCRIBE materials")) {
    echo "materials schema:\n";
    while ($row = $desc->fetch_assoc()) {
        echo "  {$row['Field']} {$row['Type']}" . ($row['Null']==='NO' ? ' NOT NULL' : '') . "\n";
    }
    echo "\n";
}

// Select rows
if ($rows = $mysqli->query("SELECT id, course_id, file_name, file_path, created_at FROM materials ORDER BY id ASC")) {
    if ($rows->num_rows === 0) {
        echo "No rows in materials table.\n";
    } else {
        echo "materials rows:\n";
        while ($r = $rows->fetch_assoc()) {
            echo sprintf("ID=%d course_id=%d file_name=%s file_path=%s created_at=%s\n", $r['id'], $r['course_id'], $r['file_name'], $r['file_path'], $r['created_at']);
        }
    }
} else {
    echo "Select failed: " . $mysqli->error . "\n";
}

$mysqli->close();
