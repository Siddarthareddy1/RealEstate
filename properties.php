<?php
include "config.php";

if (!isLoggedIn()) {
    redirect("login.php");
}

$page_title = "My Properties";

$where = "WHERE p.user_id = {$_SESSION['user_id']}";
if (isAdmin()) {
    $where = "WHERE 1=1";
}

$properties = mysqli_query($conn, "SELECT p.*, (SELECT image_name FROM property_images WHERE property_id = p.id LIMIT 1) as image 
                                   FROM properties p $where ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Real Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .property-card { transition: transform 0.2s; }
        .property-card:hover { transform: translateY(-5px); }
        .property-image { height: 200px; object-fit: cover; border-radius: 20px 20px 0 0; }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <div class="container mt-5 pt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include "sidebar.php"; ?>
            </div>
            <div class="col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4><?php echo $page_title; ?></h4>
                    <a href="add-property.php" class="btn btn-primary">+ Add Property</a>
                </div>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <?php if (mysqli_num_rows($properties) > 0): ?>
                    <div class="row">
                        <?php while ($property = mysqli_fetch_assoc($properties)): ?>
                            <div class="col-md-6 mb-4">
                                <div class="glass-card property-card h-100">
                                    <img src="<?php echo UPLOAD_DIR . ($property['image'] ?: 'default.jpg'); ?>" 
                                         class="card-img-top property-image" 
                                         onerror="this.src='https://via.placeholder.com/400x300?text=No+Image'"
                                         alt="<?php echo $property['title']; ?>">
                                    <div class="p-3">
                                        <h5 class="card-title"><?php echo $property['title']; ?></h5>
                                        <p class="text-muted mb-1">
                                            <i class="bi bi-geo-alt"></i> <?php echo $property['location']; ?>, <?php echo $property['city']; ?>
                                        </p>
                                        <p class="fw-bold" style="background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                                            <?php echo $property['purpose'] == 'rent' ? 'Rent: ' : 'Price: '; ?>
                                            $<?php echo number_format($property['price']); ?>
                                        </p>
                                        <div class="d-flex justify-content-between">
                                            <span class="badge bg-<?php echo $property['status'] == 'available' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($property['status']); ?>
                                            </span>
                                            <span class="badge bg-info"><?php echo ucfirst($property['property_type']); ?></span>
                                        </div>
                                        <div class="mt-3">
                                            <a href="edit-property.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="property-details.php?id=<?php echo $property['id']; ?>" class="btn btn-sm btn-info">View</a>
                                            <a href="delete-property.php?id=<?php echo $property['id']; ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Are you sure you want to delete this property?')">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="glass-card p-4 text-center">
                        <p class="text-muted mb-3">No properties found.</p>
                        <a href="add-property.php" class="btn btn-primary">Add your first property</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
