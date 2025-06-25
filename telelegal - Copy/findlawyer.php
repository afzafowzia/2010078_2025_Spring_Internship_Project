<?php
require_once 'config.php';

// Initialize filter variables
$searchTerm = $_GET['search'] ?? '';
$specializations = $_GET['specialization'] ?? [];
$experienceLevels = $_GET['experience'] ?? [];
$languages = $_GET['language'] ?? [];

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Base SQL query
    $sql = "SELECT l.*, GROUP_CONCAT(ln.name SEPARATOR ',') as languages 
            FROM lawyers l
            LEFT JOIN lawyer_languages ll ON l.id = ll.lawyer_id
            LEFT JOIN languages ln ON ll.language_id = ln.id";
    
    // WHERE conditions array
    $conditions = [];
    $params = [];
    
    // Add search term condition
    if (!empty($searchTerm)) {
        $conditions[] = "(l.name LIKE :search OR l.specialization LIKE :search OR l.bio LIKE :search)";
        $params[':search'] = "%$searchTerm%";
    }
    
    // Add specialization filter
    if (!empty($specializations)) {
        $placeholders = implode(',', array_fill(0, count($specializations), '?'));
        $conditions[] = "l.specialization IN ($placeholders)";
        $params = array_merge($params, $specializations);
    }
    
    // Add experience filter
    if (!empty($experienceLevels)) {
        $expConditions = [];
        foreach ($experienceLevels as $level) {
            if ($level === 'junior') {
                $expConditions[] = "l.experience <= 5";
            } elseif ($level === 'mid') {
                $expConditions[] = "(l.experience > 5 AND l.experience <= 10)";
            } elseif ($level === 'senior') {
                $expConditions[] = "l.experience > 10";
            }
        }
        if (!empty($expConditions)) {
            $conditions[] = "(" . implode(' OR ', $expConditions) . ")";
        }
    }
    
    // Add language filter
    if (!empty($languages)) {
        $conditions[] = "ln.name IN (" . implode(',', array_fill(0, count($languages), '?')) . ")";
        $params = array_merge($params, $languages);
    }
    
    // Combine all conditions
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }
    
    // Complete the query
    $sql .= " GROUP BY l.id";
    
    // Prepare and execute the query
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    
    $lawyers = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $lawyers[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'specialization' => $row['specialization'],
            'experience' => $row['experience'],
            'rating' => $row['rating'],
            'languages' => !empty($row['languages']) ? explode(',', $row['languages']) : [],
            'image' => $row['image'],
            'bio' => $row['bio']
        ];
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $lawyers = []; // Empty array instead of dummy data
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Lawyers | Tele-legal Appointment System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6a11cb;
            --secondary-color: #2575fc;
            --accent-color: #ff6b6b;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .lawyers-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 60px 0 40px;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .lawyers-header h1 {
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .lawyers-header p {
            opacity: 0.9;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .search-container {
            max-width: 800px;
            margin: -30px auto 40px;
            position: relative;
            z-index: 10;
        }
        
        .search-box {
            background: white;
            border-radius: 50px;
            padding: 15px 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .lawyer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 30px;
            transition: all 0.3s;
            height: 100%;
        }
        
        .lawyer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .lawyer-image {
            height: 200px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f5f5f5;
        }
        
        .lawyer-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .lawyer-details {
            padding: 25px;
        }
        
        .lawyer-name {
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .lawyer-specialization {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 15px;
            display: block;
        }
        
        .lawyer-rating {
            color: #ffc107;
            margin-bottom: 10px;
        }
        
        .lawyer-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .lawyer-meta span {
            display: flex;
            align-items: center;
        }
        
        .lawyer-meta i {
            margin-right: 5px;
            color: var(--primary-color);
        }
        
        .lawyer-bio {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 20px;
            
            /* Modern standard */
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            
            /* Standard property (still not widely supported) */
            display: -moz-box;
            -moz-line-clamp: 3;
            -moz-box-orient: vertical;
            
            /* Experimental standard property */
            display: box;
            line-clamp: 3;
            box-orient: vertical;
        }
        .lawyer-languages {
            margin-bottom: 20px;
        }
        
        .language-badge {
            display: inline-block;
            background-color: rgba(106, 17, 203, 0.1);
            color: var(--primary-color);
            padding: 3px 10px;
            border-radius: 50px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .btn-book {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border: none;
            width: 100%;
            padding: 10px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        
        .btn-book:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .filter-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .filter-title i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .form-check-label {
            cursor: pointer;
        }
        
        .no-lawyers {
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .no-lawyers i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <header class="lawyers-header">
        <div class="container">
            <h1><i class="fas fa-gavel me-2"></i> Our Legal Experts</h1>
            <p>Connect with qualified lawyers specializing in various legal fields. Book a consultation at your convenience.</p>
        </div>
    </header>
    
    <!-- Search and Filter Section -->
    <div class="container">
        <div class="search-container">
            <div class="search-box">
                <form method="GET" action="">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search lawyers by name, specialization..." 
                            value="<?php echo htmlspecialchars($searchTerm); ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Search</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="row">
            <!-- Filters Column -->
            <div class="col-lg-3">
                <div class="filter-section">
                    <form method="GET" action="">
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
                        
                        <h5 class="filter-title"><i class="fas fa-filter"></i> Filter Lawyers</h5>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Specialization</h6>
                            <?php 
                            $allSpecializations = ['Corporate Law', 'Criminal Defense', 'Family Law', 'Intellectual Property', 
                                                'Immigration Law', 'Personal Injury', 'Healthcare Law', 'Real Estate Law'];
                            foreach ($allSpecializations as $spec): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="specialization[]" 
                                        id="spec-<?php echo preg_replace('/\s+/', '-', strtolower($spec)); ?>" 
                                        value="<?php echo htmlspecialchars($spec); ?>"
                                        <?php if (in_array($spec, $specializations)) echo 'checked'; ?>>
                                    <label class="form-check-label" for="spec-<?php echo preg_replace('/\s+/', '-', strtolower($spec)); ?>">
                                        <?php echo htmlspecialchars($spec); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Experience</h6>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="experience[]" id="expJunior" 
                                    value="junior" <?php if (in_array('junior', $experienceLevels)) echo 'checked'; ?>>
                                <label class="form-check-label" for="expJunior">1-5 years</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="experience[]" id="expMid" 
                                    value="mid" <?php if (in_array('mid', $experienceLevels)) echo 'checked'; ?>>
                                <label class="form-check-label" for="expMid">5-10 years</label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="experience[]" id="expSenior" 
                                    value="senior" <?php if (in_array('senior', $experienceLevels)) echo 'checked'; ?>>
                                <label class="form-check-label" for="expSenior">10+ years</label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3">Languages</h6>
                            <?php 
                            // Get all languages from database
                            $langStmt = $pdo->query("SELECT * FROM languages");
                            $allLanguages = $langStmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($allLanguages as $lang): ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="language[]" 
                                        id="lang-<?php echo htmlspecialchars($lang['id']); ?>" 
                                        value="<?php echo htmlspecialchars($lang['name']); ?>"
                                        <?php if (in_array($lang['name'], $languages)) echo 'checked'; ?>>
                                    <label class="form-check-label" for="lang-<?php echo htmlspecialchars($lang['id']); ?>">
                                        <?php echo htmlspecialchars($lang['name']); ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="submit" class="btn btn-outline-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
            
            <!-- Lawyers Listing Column -->
            <div class="col-lg-9">
                <div class="row">
                    <?php if (count($lawyers) > 0): ?>
                        <?php foreach ($lawyers as $lawyer): ?>
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="lawyer-card">
                                    <div class="lawyer-image">
                                        <img src="<?php echo htmlspecialchars($lawyer['image']); ?>" alt="<?php echo htmlspecialchars($lawyer['name']); ?>">
                                    </div>
                                    <div class="lawyer-details">
                                        <h4 class="lawyer-name"><?php echo htmlspecialchars($lawyer['name']); ?></h4>
                                        <span class="lawyer-specialization"><?php echo htmlspecialchars($lawyer['specialization']); ?></span>
                                        
                                        <div class="lawyer-rating">
                                            <?php 
                                            $fullStars = floor($lawyer['rating']);
                                            $halfStar = ($lawyer['rating'] - $fullStars) >= 0.5;
                                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                            
                                            for ($i = 0; $i < $fullStars; $i++) {
                                                echo '<i class="fas fa-star"></i>';
                                            }
                                            if ($halfStar) {
                                                echo '<i class="fas fa-star-half-alt"></i>';
                                            }
                                            for ($i = 0; $i < $emptyStars; $i++) {
                                                echo '<i class="far fa-star"></i>';
                                            }
                                            ?>
                                            <span class="ms-1"><?php echo htmlspecialchars($lawyer['rating']); ?></span>
                                        </div>
                                        
                                        <div class="lawyer-meta">
                                            <span><i class="fas fa-briefcase"></i> <?php echo htmlspecialchars($lawyer['experience']); ?></span>
                                            <span><i class="fas fa-comments"></i> <?php echo htmlspecialchars($lawyer['consult_count'] ?? 0); ?>+ consults</span>
                                        </div>
                                        
                                        <p class="lawyer-bio"><?php echo htmlspecialchars($lawyer['bio']); ?></p>
                                        
                                        <div class="lawyer-languages">
                                            <?php foreach ($lawyer['languages'] as $language): ?>
                                                <span class="language-badge"><?php echo htmlspecialchars($language); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <a href="appointment.php?lawyer_id=<?php echo htmlspecialchars($lawyer['id']); ?>" class="btn btn-book">
                                            <i class="fas fa-calendar-check me-2"></i> Book Consultation
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="no-lawyers">
                                <i class="fas fa-user-tie"></i>
                                <h3>No Lawyers Available</h3>
                                <p>Currently there are no lawyers matching your criteria. Please try different filters.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Lawyers pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
<script>
$(document).ready(function() {
    // Handle filter form submission without page reload (AJAX)
    $('.filter-section form').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        var formData = $(this).serialize();
        
        // Send AJAX request
        $.ajax({
            url: window.location.pathname,
            type: 'GET',
            data: formData,
            success: function(response) {
                // Extract the lawyer listings from the response
                var newContent = $(response).find('.col-lg-9').html();
                
                // Update the listings
                $('.col-lg-9').html(newContent);
                
                // Update URL without reload
                history.pushState(null, '', window.location.pathname + '?' + formData);
            },
            error: function() {
                alert('Error applying filters. Please try again.');
            }
        });
    });
    
    // Handle search form submission
    $('.search-box form').on('submit', function(e) {
        e.preventDefault();
        $(this).closest('form').submit(); // Regular form submission
    });
});
</script>
</body>
</html>