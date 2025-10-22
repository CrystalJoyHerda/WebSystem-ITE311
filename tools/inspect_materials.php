<?php
require __DIR__ . '/../vendor/autoload.php';
// Bootstrap CodeIgniter for DB access
chdir(__DIR__ . '/../');
$paths = require 'app/Config/Paths.php';
// Load environment and framework
// Minimal DB access using Config
$db = Config\Database::connect();
$builder = $db->table('materials');
$rows = $builder->get()->getResultArray();
if (empty($rows)) {
    echo "No materials rows found.\n";
    exit(0);
}
foreach ($rows as $r) {
    echo "ID: {$r['id']}, course_id: {$r['course_id']}, file_name: {$r['file_name']}, file_path: {$r['file_path']}\n";
}
