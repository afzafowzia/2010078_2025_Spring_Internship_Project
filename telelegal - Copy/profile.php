<?php
require_once 'config.php';
require_once 'functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle profile picture upload
// In your profile update logic
if (!empty($_FILES['profile_picture']['name'])) {
    $uploadResult = handleProfileUpload($_FILES['profile_picture'], $_SESSION['user_id']);
    
    if (isset($uploadResult['error'])) {
        $error = $uploadResult['error'];
    } else {
        // Update database with new filename
        $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        $stmt->execute([$uploadResult['filename'], $_SESSION['user_id']]);
        $_SESSION['profile_picture'] = $uploadResult['filename'];
        $success = "Profile picture updated successfully!";
    }
}

// Get current user data
$user = [];
try {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching user data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Settings | LEXASK</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        .profile-picture {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #6f42c1;
        }
        .upload-btn {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }
        .upload-btn input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Profile Settings</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($successMsg)): ?>
                            <div class="alert alert-success"><?php echo $successMsg; ?></div>
                        <?php endif; ?>
                        <?php if (isset($uploadError)): ?>
                            <div class="alert alert-danger"><?php echo $uploadError; ?></div>
                        <?php endif; ?>
                        
                        <div class="text-center mb-4">
                            <img src="uploads/<?php echo htmlspecialchars($user['profile_picture'] ?? 'default.jpg'); ?>" 
                                 class="profile-picture mb-2" 
                                 alt="Profile Picture">
                            <form method="POST" enctype="multipart/form-data" class="mt-3">
                                <div class="upload-btn btn btn-primary">
                                    <i class="bi bi-camera-fill me-2"></i>Change Photo
                                    <input type="file" name="profile_picture" accept="image/*" required>
                                </div>
                                <button type="submit" class="btn btn-success ms-2">
                                    <i class="bi bi-check-circle-fill me-2"></i>Save
                                </button>
                            </form>
                            <small class="text-muted d-block mt-2">JPEG, PNG or GIF (Max 2MB)</small>
                        </div>
                        
                        <!-- Other profile fields -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show preview of selected image
        document.querySelector('input[name="profile_picture"]').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-picture').src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>