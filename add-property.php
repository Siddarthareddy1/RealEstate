<?php
include "config.php";

if (!isLoggedIn()) {
    redirect("login.php");
}

$error = "";
$success = "";

if (isset($_POST['add_property'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = sanitize($_POST['price']);
    $location = sanitize($_POST['location']);
    $city = sanitize($_POST['city']);
    $state = sanitize($_POST['state']);
    $zip_code = sanitize($_POST['zip_code']);
    $property_type = sanitize($_POST['property_type']);
    $bedrooms = (int)$_POST['bedrooms'];
    $bathrooms = (int)$_POST['bathrooms'];
    $area = sanitize($_POST['area']);
    $purpose = sanitize($_POST['purpose']);
    $status = 'available';
    
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_approved = isAgent() ? 1 : 0;
    
    $sql = "INSERT INTO properties (user_id, title, description, price, location, city, state, zip_code, property_type, bedrooms, bathrooms, area, purpose, status, is_featured, is_approved) 
            VALUES ('{$_SESSION['user_id']}', '$title', '$description', '$price', '$location', '$city', '$state', '$zip_code', '$property_type', '$bedrooms', '$bathrooms', '$area', '$purpose', '$status', $is_featured, $is_approved)";
    
    if (mysqli_query($conn, $sql)) {
        $property_id = mysqli_insert_id($conn);
        
        if (!empty($_FILES['images']['name'][0])) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $upload_dir = UPLOAD_DIR;
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            foreach ($_FILES['images']['name'] as $key => $name) {
                if ($_FILES['images']['error'][$key] === 0) {
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (in_array($ext, $allowed)) {
                        $new_name = uniqid() . '.' . $ext;
                        $target = $upload_dir . $new_name;
                        
                        if (move_uploaded_file($_FILES['images']['tmp_name'][$key], $target)) {
                            $is_primary = ($key === 0) ? 1 : 0;
                            mysqli_query($conn, "INSERT INTO property_images (property_id, image_name, is_primary) VALUES ($property_id, '$new_name', $is_primary)");
                        }
                    }
                }
            }
        }
        
        $success = "Property added successfully!";
    } else {
        $error = "Failed to add property. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - DreamHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <div class="container mt-5 pt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include "sidebar.php"; ?>
            </div>
            <div class="col-md-9">
                <div class="glass-card p-4">
                    <h4>Add New Property</h4>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Title *</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Property Type *</label>
                                    <select name="property_type" class="form-select" required>
                                        <option value="">Select Type</option>
                                        <option value="house">House</option>
                                        <option value="apartment">Apartment</option>
                                        <option value="land">Land</option>
                                        <option value="commercial">Commercial</option>
                                        <option value="villa">Villa</option>
                                        <option value="office">Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price *</label>
                                    <input type="number" name="price" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Purpose</label>
                                    <select name="purpose" class="form-select">
                                        <option value="sale">For Sale</option>
                                        <option value="rent">For Rent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location/Address *</label>
                                    <input type="text" name="location" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City *</label>
                                    <input type="text" name="city" class="form-control" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Zip Code</label>
                                    <input type="text" name="zip_code" class="form-control">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Bedrooms</label>
                                    <input type="number" name="bedrooms" class="form-control" value="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Bathrooms</label>
                                    <input type="number" name="bathrooms" class="form-control" value="0">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Area (sq ft)</label>
                                    <input type="number" name="area" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Images</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            </div>
                            <?php if (isAgent()): ?>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_featured" class="form-check-input" id="featured">
                                <label class="form-check-label" for="featured">Mark as Featured</label>
                            </div>
                            <?php endif; ?>
                            <button type="submit" name="add_property" class="btn btn-primary">Add Property</button>
                            <a href="properties.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
