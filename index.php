<?php
include "config.php";

$featured_properties = mysqli_query($conn, "SELECT p.*, (SELECT image_name FROM property_images WHERE property_id = p.id LIMIT 1) as image 
                                              FROM properties p 
                                              WHERE p.is_approved = 1 AND p.status = 'available' 
                                              ORDER BY p.is_featured DESC, p.created_at DESC LIMIT 6");

$total_properties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM properties WHERE is_approved = 1"));
$total_cities = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT city) as count FROM properties WHERE city != '' AND is_approved = 1"));
$total_agents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'agent' AND status = 'active'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DreamHome - Find Your Dream Property</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <div class="hero">
        <div class="container">
            <h1>Find Your Dream Home</h1>
            <p class="lead">Discover luxurious properties that match your lifestyle</p>
            
            <div class="search-box mt-4">
                <form method="GET" action="search.php">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by location, title...">
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-select">
                                <option value="">All Types</option>
                                <option value="house">House</option>
                                <option value="apartment">Apartment</option>
                                <option value="villa">Villa</option>
                                <option value="land">Land</option>
                                <option value="commercial">Commercial</option>
                                <option value="office">Office</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="purpose" class="form-select">
                                <option value="">All Purpose</option>
                                <option value="sale">For Sale</option>
                                <option value="rent">For Rent</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-2"></i>Search
                            </button>
    </div>
    </header>
    
    <main>
    <div class="container mt-5 pt-4">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stats-card">
                    <h2><?php echo $total_properties['count']; ?></h2>
                    <p><i class="bi bi-building"></i>Properties Listed</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h2><?php echo $total_cities['count']; ?></h2>
                    <p><i class="bi bi-geo-alt"></i>Cities Covered</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stats-card">
                    <h2><?php echo $total_agents['count']; ?></h2>
                    <p><i class="bi bi-people"></i>Expert Agents</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container mt-5 pt-4">
        <div class="section-title">
            <h2>Featured Properties</h2>
            <p>Handpicked luxury properties just for you</p>
        </div>
        
        <?php if (mysqli_num_rows($featured_properties) > 0): ?>
            <div class="row g-4">
                <?php while ($property = mysqli_fetch_assoc($featured_properties)): ?>
                    <div class="col-md-4">
                        <div class="property-card card h-100">
                            <div class="position-relative overflow-hidden" style="height: 240px;">
                                <img src="<?php echo UPLOAD_DIR . ($property['image'] ?: 'default.jpg'); ?>" 
                                     class="card-img-top w-100 h-100"
                                     style="object-fit: cover;"
                                     onerror="this.src='https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=400&q=80'"
                                     alt="<?php echo $property['title']; ?>">
                                <?php if ($property['is_featured']): ?>
                                    <span class="property-badge">Featured</span>
                                <?php endif; ?>
                                <span class="property-badge" style="left: auto; right: 15px; background: <?php echo $property['purpose'] == 'rent' ? 'linear-gradient(135deg, #10b981, #059669)' : 'var(--gradient-primary)'; ?>">
                                    <i class="bi <?php echo $property['purpose'] == 'rent' ? 'bi-calendar-check' : 'bi-key'; ?> me-1"></i>
                                    <?php echo ucfirst($property['purpose']); ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $property['title']; ?></h5>
                                <p class="text-muted mb-2" style="font-size: 14px;">
                                    <i class="bi bi-geo-alt"></i> <?php echo $property['location']; ?>, <?php echo $property['city']; ?>
                                </p>
                                <p class="property-price mb-3">
                                    $<?php echo number_format($property['price']); ?>
                                    <small style="font-size: 12px; color: var(--text-muted);">
                                        <?php echo $property['purpose'] == 'rent' ? '/month' : ''; ?>
                                    </small>
                                </p>
                                <div class="property-features mb-3">
                                    <span><i class="bi bi-door-open"></i> <?php echo $property['bedrooms']; ?> Beds</span>
                                    <span><i class="bi bi-droplet"></i> <?php echo $property['bathrooms']; ?> Baths</span>
                                    <span><i class="bi bi-arrows-angle-expand"></i> <?php echo number_format($property['area']); ?> sqft</span>
                                </div>
                                <a href="property-details.php?id=<?php echo $property['id']; ?>" class="btn btn-primary w-100">
                                    <i class="bi bi-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="glass-card p-5 text-center">
                <i class="bi bi-building" style="font-size: 4rem; color: var(--text-muted);"></i>
                <h4 class="mt-3">No properties available yet</h4>
                <p class="text-muted">Be the first to list your property!</p>
            </div>
        <?php endif; ?>
        
        <div class="text-center mt-5">
            <a href="search.php" class="btn btn-primary">
                <i class="bi bi-grid me-2"></i>View All Properties
            </a>
        </div>
    </div>
    
    <div class="py-5" style="background: linear-gradient(rgba(99, 102, 241, 0.9), rgba(139, 92, 246, 0.9)), url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1920&q=80'); background-size: cover; background-position: center; background-attachment: fixed;">
        <div class="container text-center py-4">
            <h2 class="mb-3">Ready to Find Your Dream Home?</h2>
            <p class="mb-4" style="opacity: 0.9;">Join thousands of happy customers who found their perfect property</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="search.php" class="btn btn-light" style="border-radius: 30px; padding: 12px 35px; font-weight: 600;">
                    <i class="bi bi-search me-2"></i>Browse Properties
                </a>
                <a href="register.php" class="btn btn-outline-light" style="border-radius: 30px; padding: 12px 35px; font-weight: 600;">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </a>
            </div>
        </div>
    </div>
    
    <div class="container mt-5 pt-4">
        <div class="section-title">
            <h2>Why Choose Us?</h2>
            <p>Experience premium real estate service</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-building"></i>
                    </div>
                    <h4>Premium Properties</h4>
                    <p>Access to exclusive luxury homes and premium listings across top locations</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4>Verified Listings</h4>
                    <p>All properties are verified by our expert team for your peace of mind</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h4>24/7 Support</h4>
                    <p>Round-the-clock customer support to assist you with any queries</p>
                </div>
            </div>
        </div>
    </div>
    </main>
    
    <footer class="mt-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4">
                    <h5><i class="bi bi-house-heart-fill me-2"></i>DreamHome</h5>
                    <p style="opacity: 0.7;">Your trusted partner in finding the perfect luxury property. We make your dream home a reality.</p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="glass-card p-2 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="glass-card p-2 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="glass-card p-2 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="glass-card p-2 rounded-circle" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <a href="index.php"><i class="bi bi-chevron-right me-2"></i>Home</a>
                    <a href="search.php"><i class="bi bi-chevron-right me-2"></i>Properties</a>
                    <a href="contact.php"><i class="bi bi-chevron-right me-2"></i>Contact</a>
                    <a href="login.php"><i class="bi bi-chevron-right me-2"></i>Login</a>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <p><i class="bi bi-geo-alt me-2"></i>123 Luxury Lane, Beverly Hills, CA</p>
                    <p><i class="bi bi-envelope me-2"></i>info@dreamhome.com</p>
                    <p><i class="bi bi-phone me-2"></i>+1 234 567 8900</p>
                </div>
            </div>
            <hr style="opacity: 0.15; margin: 40px 0 20px;">
            <div class="text-center" style="opacity: 0.6;">
                <p>&copy; 2026 DreamHome. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
