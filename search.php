<?php
include "config.php";

$page_title = "Search Properties";

$where = "WHERE p.is_approved = 1";
$params = [];

if (isset($_GET['search'])) {
    $search = sanitize($_GET['search']);
    if ($search) {
        $where .= " AND (p.title LIKE '%$search%' OR p.location LIKE '%$search%' OR p.city LIKE '%$search%')";
    }
}

if (isset($_GET['city']) && $_GET['city']) {
    $city = sanitize($_GET['city']);
    $where .= " AND p.city = '$city'";
}

if (isset($_GET['property_type']) && $_GET['property_type']) {
    $property_type = sanitize($_GET['property_type']);
    $where .= " AND p.property_type = '$property_type'";
}

if (isset($_GET['purpose']) && $_GET['purpose']) {
    $purpose = sanitize($_GET['purpose']);
    $where .= " AND p.purpose = '$purpose'";
}

if (isset($_GET['min_price']) && $_GET['min_price']) {
    $min_price = (int)$_GET['min_price'];
    $where .= " AND p.price >= $min_price";
}

if (isset($_GET['max_price']) && $_GET['max_price']) {
    $max_price = (int)$_GET['max_price'];
    $where .= " AND p.price <= $max_price";
}

if (isset($_GET['bedrooms']) && $_GET['bedrooms']) {
    $bedrooms = (int)$_GET['bedrooms'];
    $where .= " AND p.bedrooms >= $bedrooms";
}

$sql = "SELECT p.*, (SELECT image_name FROM property_images WHERE property_id = p.id LIMIT 1) as image 
        FROM properties p $where ORDER BY p.created_at DESC";
$properties = mysqli_query($conn, $sql);

$cities = mysqli_query($conn, "SELECT DISTINCT city FROM properties WHERE city != '' ORDER BY city");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - DreamHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <main>
    <div class="page-header">
        <div class="container">
            <h1><i class="bi bi-building me-3"></i>Find Your Property</h1>
            <p>Browse our collection of premium properties</p>
        </div>
    </div>
    
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-3">
                <div class="glass-card p-4">
                    <h5 class="mb-4"><i class="bi bi-funnel me-2"></i>Filters</h5>
                    <form method="GET">
                        <div class="mb-3">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" class="form-control" placeholder="Search properties..." value="<?php echo $_GET['search'] ?? ''; ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">City</label>
                            <select name="city" class="form-select">
                                <option value="">All Cities</option>
                                <?php while ($c = mysqli_fetch_assoc($cities)): ?>
                                    <option value="<?php echo $c['city']; ?>" <?php echo ($_GET['city'] ?? '') == $c['city'] ? 'selected' : ''; ?>>
                                        <?php echo $c['city']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Property Type</label>
                            <select name="property_type" class="form-select">
                                <option value="">All Types</option>
                                <option value="house" <?php echo ($_GET['property_type'] ?? '') == 'house' ? 'selected' : ''; ?>>House</option>
                                <option value="apartment" <?php echo ($_GET['property_type'] ?? '') == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                                <option value="land" <?php echo ($_GET['property_type'] ?? '') == 'land' ? 'selected' : ''; ?>>Land</option>
                                <option value="commercial" <?php echo ($_GET['property_type'] ?? '') == 'commercial' ? 'selected' : ''; ?>>Commercial</option>
                                <option value="villa" <?php echo ($_GET['property_type'] ?? '') == 'villa' ? 'selected' : ''; ?>>Villa</option>
                                <option value="office" <?php echo ($_GET['property_type'] ?? '') == 'office' ? 'selected' : ''; ?>>Office</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Purpose</label>
                            <select name="purpose" class="form-select">
                                <option value="">All</option>
                                <option value="sale" <?php echo ($_GET['purpose'] ?? '') == 'sale' ? 'selected' : ''; ?>>For Sale</option>
                                <option value="rent" <?php echo ($_GET['purpose'] ?? '') == 'rent' ? 'selected' : ''; ?>>For Rent</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Min Price</label>
                                <input type="number" name="min_price" class="form-control" placeholder="0" value="<?php echo $_GET['min_price'] ?? ''; ?>">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Max Price</label>
                                <input type="number" name="max_price" class="form-control" placeholder="Any" value="<?php echo $_GET['max_price'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Bedrooms</label>
                            <select name="bedrooms" class="form-select">
                                <option value="">Any</option>
                                <option value="1" <?php echo ($_GET['bedrooms'] ?? '') == '1' ? 'selected' : ''; ?>>1+</option>
                                <option value="2" <?php echo ($_GET['bedrooms'] ?? '') == '2' ? 'selected' : ''; ?>>2+</option>
                                <option value="3" <?php echo ($_GET['bedrooms'] ?? '') == '3' ? 'selected' : ''; ?>>3+</option>
                                <option value="4" <?php echo ($_GET['bedrooms'] ?? '') == '4' ? 'selected' : ''; ?>>4+</option>
                                <option value="5" <?php echo ($_GET['bedrooms'] ?? '') == '5' ? 'selected' : ''; ?>>5+</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-search me-2"></i>Apply Filters
                        </button>
                        <a href="search.php" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                        </a>
                    </form>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0"><i class="bi bi-house-heart me-2"></i>Properties Found: <span class="text-primary"><?php echo mysqli_num_rows($properties); ?></span></h4>
                </div>
                
                <?php if (mysqli_num_rows($properties) > 0): ?>
                    <div class="row g-4">
                        <?php while ($property = mysqli_fetch_assoc($properties)): ?>
                            <div class="col-md-6">
                                <div class="property-card card h-100">
                                    <div class="position-relative overflow-hidden" style="height: 220px;">
                                        <img src="<?php echo UPLOAD_DIR . ($property['image'] ?: 'default.jpg'); ?>" 
                                             class="card-img-top w-100 h-100"
                                             style="object-fit: cover;"
                                             onerror="this.src='https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=400&q=80'"
                                             alt="<?php echo $property['title']; ?>">
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
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge" style="background: <?php echo $property['status'] == 'available' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #f59e0b, #d97706)'; ?>;">
                                                <?php echo ucfirst($property['status']); ?>
                                            </span>
                                            <a href="property-details.php?id=<?php echo $property['id']; ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i>View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="glass-card p-5 text-center">
                        <i class="bi bi-search" style="font-size: 4rem; color: var(--text-muted);"></i>
                        <h4 class="mt-3">No properties found</h4>
                        <p class="text-muted">Try adjusting your filters to find more properties</p>
                        <a href="search.php" class="btn btn-primary">
                            <i class="bi bi-arrow-counterclockwise me-2"></i>Reset Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <footer class="mt-5">
        <div class="container">
            <div class="row g-5">
                <div class="col-md-4">
                    <h5><i class="bi bi-house-heart-fill me-2"></i>DreamHome</h5>
                    <p style="opacity: 0.7;">Your trusted partner in finding the perfect luxury property.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <a href="index.php"><i class="bi bi-chevron-right me-2"></i>Home</a>
                    <a href="search.php"><i class="bi bi-chevron-right me-2"></i>Properties</a>
                    <a href="contact.php"><i class="bi bi-chevron-right me-2"></i>Contact</a>
                </div>
                <div class="col-md-4">
                    <h5>Contact</h5>
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
