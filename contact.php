<?php
include "config.php";

$success = "";
$error = "";

if (isset($_POST['send_message'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $message = sanitize($_POST['message']);
    
    $sql = "INSERT INTO inquiries (property_id, name, email, phone, message) VALUES (0, '$name', '$email', '$phone', '$message')";
    
    if (mysqli_query($conn, $sql)) {
        $success = "Message sent successfully! We will contact you soon.";
    } else {
        $error = "Failed to send message. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - DreamHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <main>
    <div class="page-header">
        <div class="container">
            <h1><i class="bi bi-envelope me-3"></i>Contact Us</h1>
            <p>Have questions? We'd love to hear from you</p>
        </div>
    </div>
    
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="glass-card p-4">
                    <h4 class="mb-4"><i class="bi bi-send me-2"></i>Send us a Message</h4>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Your Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo isLoggedIn() ? $_SESSION['name'] : ''; ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="<?php echo isLoggedIn() ? $_SESSION['email'] : ''; ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Message</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="How can we help you?" required></textarea>
                        </div>
                        <button type="submit" name="send_message" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="glass-card p-4">
                    <h4 class="mb-4"><i class="bi bi-geo-alt me-2"></i>Get In Touch</h4>
                    
                    <div class="mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="glass-card p-3 rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-geo-alt"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Address</h6>
                                <p class="text-muted mb-0" style="font-size: 14px;">123 Luxury Lane, Beverly Hills, CA</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="glass-card p-3 rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-telephone"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Phone</h6>
                                <p class="text-muted mb-0" style="font-size: 14px;">+1 234 567 8900</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="glass-card p-3 rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Email</h6>
                                <p class="text-muted mb-0" style="font-size: 14px;">info@dreamhome.com</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="glass-card p-3 rounded-circle me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Working Hours</h6>
                                <p class="text-muted mb-0" style="font-size: 14px;">Mon - Fri: 9AM - 6PM</p>
                            </div>
                        </div>
                    </div>
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
