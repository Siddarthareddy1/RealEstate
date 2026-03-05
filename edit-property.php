<?php
include "config.php";

if (!isLoggedIn()) {
    redirect("login.php");
}

$property_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$check_property = mysqli_query($conn, "SELECT * FROM properties WHERE id = $property_id AND user_id = {$_SESSION['user_id']}");
if (mysqli_num_rows($check_property) === 0 && !isAdmin()) {
    redirect("properties.php");
}

$property = mysqli_fetch_assoc($check_property);

$error = "";
$success = "";

if (isset($_POST['update_property'])) {
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
    $status = sanitize($_POST['status']);
    
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $sql = "UPDATE properties SET 
            title = '$title', description = '$description', price = '$price', 
            location = '$location', city = '$city', state = '$state', 
            zip_code = '$zip_code', property_type = '$property_type', 
            bedrooms = $bedrooms, bathrooms = $bathrooms, area = '$area', 
            purpose = '$purpose', status = '$status', is_featured = $is_featured 
            WHERE id = $property_id";
    
    if (mysqli_query($conn, $sql)) {
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
                            mysqli_query($conn, "INSERT INTO property_images (property_id, image_name) VALUES ($property_id, '$new_name')");
                        }
                    }
                }
            }
        }
        
        $success = "Property updated successfully!";
        $property = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM properties WHERE id = $property_id"));
    } else {
        $error = "Failed to update property.";
    }
}

if (isset($_POST['delete_image'])) {
    $image_id = (int)$_POST['image_id'];
    mysqli_query($conn, "DELETE FROM property_images WHERE id = $image_id");
    $success = "Image deleted.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Property - Real Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include "navbar.php"; ?>
    
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <?php include "sidebar.php"; ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h4>Edit Property</h4>
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
                                    <input type="text" name="title" class="form-control" value="<?php echo $property['title']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Property Type *</label>
                                    <select name="property_type" class="form-select" required>
                                        <option value="house" <?php echo $property['property_type'] == 'house' ? 'selected' : ''; ?>>House</option>
                                        <option value="apartment" <?php echo $property['property_type'] == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                                        <option value="land" <?php echo $property['property_type'] == 'land' ? 'selected' : ''; ?>>Land</option>
                                        <option value="commercial" <?php echo $property['property_type'] == 'commercial' ? 'selected' : ''; ?>>Commercial</option>
                                        <option value="villa" <?php echo $property['property_type'] == 'villa' ? 'selected' : ''; ?>>Villa</option>
                                        <option value="office" <?php echo $property['property_type'] == 'office' ? 'selected' : ''; ?>>Office</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"><?php echo $property['description']; ?></textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price *</label>
                                    <input type="number" name="price" class="form-control" value="<?php echo $property['price']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Purpose</label>
                                    <select name="purpose" class="form-select">
                                        <option value="sale" <?php echo $property['purpose'] == 'sale' ? 'selected' : ''; ?>>For Sale</option>
                                        <option value="rent" <?php echo $property['purpose'] == 'rent' ? 'selected' : ''; ?>>For Rent</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Location/>
                                    <inputAddress *</label type="text" name="location" class="form-control" value="<?php echo $property['location']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">City *</label>
                                    <input type="text" name="city" class="form-control" value="<?php echo $property['city']; ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">State</label>
                                    <input type="text" name="state" class="form-control" value="<?php echo $property['state']; ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Zip Code</label>
                                    <input type="text" name="zip_code" class="form-control" value="<?php echo $property['zip_code']; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Bedrooms</label>
                                    <input type="number" name="bedrooms" class="form-control" value="<?php echo $property['bedrooms']; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Bathrooms</label>
                                    <input type="number" name="bathrooms" class="form-control" value="<?php echo $property['bathrooms']; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Area (sq ft)</label>
                                    <input type="number" name="area" class="form-control" value="<?php echo $property['area']; ?>">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="available" <?php echo $property['status'] == 'available' ? 'selected' : ''; ?>>Available</option>
                                        <option value="sold" <?php echo $property['status'] == 'sold' ? 'selected' : ''; ?>>Sold</option>
                                        <option value="rented" <?php echo $property['status'] == 'rented' ? 'selected' : ''; ?>>Rented</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Add More Images</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            </div>
                            <?php if (isAgent()): ?>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_featured" class="form-check-input" id="featured" <?php echo $property['is_featured'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="featured">Mark as Featured</label>
                            </div>
                            <?php endif; ?>
                            <button type="submit" name="update_property" class="btn btn-primary">Update Property</button>
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
