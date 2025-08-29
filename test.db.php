<?php
echo "<h2>Simple Database Connection Test</h2>";

// Your actual database settings from .env
$hostname = 'localhost';
$username = 'lms';           // ← From your .env
$password = 'lms';           // ← From your .env  
$database = 'lms_herda';     // ← From your .env

try {
    // Test MySQL connection
    $mysqli = new mysqli($hostname, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        echo "❌ Connection failed: " . $mysqli->connect_error . "<br>";
    } else {
        echo "✅ Database connection: SUCCESS<br>";
        echo "Database name: " . $database . "<br>";
        
        // Check if users table exists
        $result = $mysqli->query("SHOW TABLES LIKE 'users'");
        if ($result->num_rows > 0) {
            echo "✅ Users table: EXISTS<br>";
            
            // Count users
            $countResult = $mysqli->query("SELECT COUNT(*) as count FROM users");
            $count = $countResult->fetch_assoc()['count'];
            echo "Current user count: " . $count . "<br>";
            
            // Test insert
            $testName = 'Test User ' . time();
            $testEmail = 'test' . time() . '@example.com';
            $testPassword = password_hash('123456', PASSWORD_DEFAULT);
            
            $stmt = $mysqli->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $testName, $testEmail, $testPassword);
            
            if ($stmt->execute()) {
                echo "✅ Test insert: SUCCESS (ID: " . $mysqli->insert_id . ")<br>";
                
                // Clean up test data
                $mysqli->query("DELETE FROM users WHERE email = '$testEmail'");
                echo "✅ Test data cleaned up<br>";
            } else {
                echo "❌ Test insert: FAILED - " . $stmt->error . "<br>";
            }
            
        } else {
            echo "❌ Users table: NOT FOUND<br>";
        }
        
        $mysqli->close();
    }
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "<br>";
}
?>