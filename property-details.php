<?php
include "config.php";

$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$property = getPropertyById($property_id);

if (!$property) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Property not found!</div></div>";
    exit;
}

$images = getPropertyImages($property_id);
$primary_image = getPrimaryImage($property_id);

$error = "";
$success = "";

if (isset($_POST['send_inquiry'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $message = sanitize($_POST['message']);
    $user_id = isLoggedIn() ? $_SESSION['user_id'] : NULL;
    
    $sql = "INSERT INTO inquiries (property_id, user_id, name, email, phone, message) 
            VALUES ($property_id, " . ($user_id ? $user_id : "NULL") . ", '$name', '$email', '$phone', '$message')";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Inquiry sent successfully! The owner will contact you soon.";
    } else {
        $error = "Failed to send inquiry. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $property['title']; ?> - DreamHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <main>
    <div class="page-header">
        <div class="container">
            <h1><i class="bi bi-house-heart me-3"></i><?php echo $property['title']; ?></h1>
            <p><i class="bi bi-geo-alt me-2"></i><?php echo $property['location']; ?>, <?php echo $property['city']; ?></p>
        </div>
    </div>
    
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="glass-card p-0 overflow-hidden">
                    <img id="mainImage" src="<?php echo UPLOAD_DIR . $primary_image; ?>" 
                         class="w-100"
                         style="height: 450px; object-fit: cover;"
                         onerror="this.src='https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=800&q=80'"
                         alt="<?php echo $property['title']; ?>">
                </div>
                
                <div class="glass-card p-4 mt-4">
                    <h4 class="mb-4"><i class="bi bi-info-circle me-2"></i>Description</h4>
                    <p class="text-secondary"><?php echo nl2br($property['description']); ?></p>
                    
                    <h5 class="mt-4 mb-3"><i class="bi bi-list-ul me-2"></i>Property Details</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-house me-2 text-primary"></i><strong>Type:</strong> <?php echo ucfirst($property['property_type']); ?></li>
                                <li class="mb-2"><i class="bi bi-door-open me-2 text-primary"></i><strong>Bedrooms:</strong> <?php echo $property['bedrooms']; ?></li>
                                <li class="mb-2"><i class="bi bi-droplet me-2 text-primary"></i><strong>Bathrooms:</strong> <?php echo $property['bathrooms']; ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-rulers me-2 text-primary"></i><strong>Area:</strong> <?php echo number_format($property['area']); ?> sqft</li>
                                <li class="mb-2"><i class="bi bi-tag me-2 text-primary"></i><strong>Purpose:</strong> <?php echo ucfirst($property['purpose']); ?></li>
                                <li class="mb-2"><i class="bi bi-check-circle me-2 text-primary"></i><strong>Status:</strong> <?php echo ucfirst($property['status']); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="glass-card p-4 mb-4">
                    <div class="text-center mb-4">
                        <h2 class="property-price mb-2">$<?php echo number_format($property['price']); ?></h2>
                        <p class="text-muted"><?php echo $property['purpose'] == 'rent' ? 'Per Month' : 'Fixed Price'; ?></p>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-4 pb-4" style="border-bottom: 1px solid var(--glass-border);">
                        <div class="text-center">
                            <h5 class="mb-0"><?php echo $property['bedrooms']; ?></h5>
                            <small class="text-muted">Beds</small>
                        </div>
                        <div class="text-center">
                            <h5 class="mb-0"><?php echo $property['bathrooms']; ?></h5>
                            <small class="text-muted">Baths</small>
                        </div>
                        <div class="text-center">
                            <h5 class="mb-0"><?php echo number_format($property['area']); ?></h5>
                            <small class="text-muted">Sqft</small>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="mb-3"><i class="bi bi-person me-2"></i>Contact Agent</h6>
                        <p class="mb-1"><i class="bi bi-person me-2"></i><?php echo $property['owner_name']; ?></p>
                        <p class="mb-1"><i class="bi bi-envelope me-2"></i><?php echo $property['owner_email']; ?></p>
                        <p><i class="bi bi-phone me-2"></i><?php echo $property['owner_phone']; ?></p>
                    </div>
                </div>
                
                <div class="glass-card p-4">
                    <h5 class="mb-4"><i class="bi bi-send me-2"></i>Send Inquiry</h5>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php else: ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo isLoggedIn() ? $_SESSION['name'] : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo isLoggedIn() ? $_SESSION['email'] : ''; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Message</label>
                                <textarea name="message" class="form-control" rows="4" required>I'm interested in this property. Please contact me.</textarea>
                            </div>
                            <button type="submit" name="send_inquiry" class="btn btn-primary w-100">
                                <i class="bi bi-send me-2"></i>Send Inquiry
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    </main>
    
    <footer>
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
