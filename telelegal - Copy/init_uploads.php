<?php
// Define the upload directory path
$uploadDir = __DIR__ . '/uploads/';

// Create directory if it doesn't exist
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        die("Failed to create upload directory");
    }
    echo "Upload directory created successfully<br>";
}

// Create .htaccess for security
$htaccessContent = <<<HTACCESS
# Prevent execution of uploaded files
<FilesMatch "\.(php|php5|phtml)$">
    Require all denied
</FilesMatch>

# Prevent directory listing
Options -Indexes

# Restrict access to specific file types
<FilesMatch "\.(jpg|jpeg|png|gif)$">
    Require all granted
</FilesMatch>
HTACCESS;

file_put_contents($uploadDir . '.htaccess', $htaccessContent);
echo ".htaccess file created<br>";

// Add default profile picture
$defaultImage = file_get_contents('https://via.placeholder.com/150');
file_put_contents($uploadDir . 'default.jpg', $defaultImage);
echo "Default profile picture added<br>";

echo "Upload directory setup complete!";
?>