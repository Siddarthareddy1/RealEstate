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

$images = getPropertyImages($property_id);
foreach ($images as $img) {
    $file_path = UPLOAD_DIR . $img['image_name'];
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

mysqli_query($conn, "DELETE FROM properties WHERE id = $property_id");

$_SESSION['success'] = "Property deleted successfully!";
redirect("properties.php");
