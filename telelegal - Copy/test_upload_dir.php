<?php
require 'config.php';

echo "<h2>Upload Directory Test</h2>";
echo "Checking: " . UPLOAD_DIR . "<br><br>";

// Check if directory exists
if (file_exists(UPLOAD_DIR)) {
    echo "✅ Upload directory exists<br>";
    
    // Check if writable
    if (is_writable(UPLOAD_DIR)) {
        echo "✅ Directory is writable<br>";
        
        // Test file creation
        $testFile = UPLOAD_DIR . 'test_file.txt';
        if (file_put_contents($testFile, 'test content')) {
            echo "✅ File creation successful<br>";
            unlink($testFile); // Clean up
        } else {
            echo "❌ Could not create test file<br>";
        }
    } else {
        echo "❌ Directory is NOT writable<br>";
    }
} else {
    echo "❌ Directory does NOT exist<br>";
    echo "Trying to create it...<br>";
    
    if (mkdir(UPLOAD_DIR, 0755, true)) {
        echo "✅ Directory created successfully<br>";
    } else {
        echo "❌ Failed to create directory<br>";
        echo "Please manually create an 'uploads' folder in: " . dirname(__DIR__);
    }
}

echo "<h3>Directory Contents:</h3>";
if (file_exists(UPLOAD_DIR)) {
    echo "<pre>";
    print_r(scandir(UPLOAD_DIR));
    echo "</pre>";
}
?>