<?php
include "config.php";

if (!isLoggedIn()) {
    redirect("login.php");
}

$user_id = $_SESSION['user_id'];

$total_properties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM properties WHERE user_id = $user_id"));
$available_properties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM properties WHERE user_id = $user_id AND status = 'available'"));
$sold_properties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM properties WHERE user_id = $user_id AND status = 'sold'"));
$total_inquiries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM inquiries i JOIN properties p ON i.property_id = p.id WHERE p.user_id = $user_id"));

$recent_properties = mysqli_query($conn, "SELECT p.*, (SELECT image_name FROM property_images WHERE property_id = p.id LIMIT 1) as image 
                                           FROM properties p WHERE p.user_id = $user_id ORDER BY p.created_at DESC LIMIT 5");

$recent_inquiries = mysqli_query($conn, "SELECT i.*, p.title as property_title 
                                           FROM inquiries i 
                                           JOIN properties p ON i.property_id = p.id 
                                           WHERE p.user_id = $user_id 
                                           ORDER BY i.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DreamHome</title>
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
                <h3>Welcome, <?php echo $_SESSION['name']; ?>!</h3>
                
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="glass-card text-center mb-3 p-3">
                            <h5 class="card-title">Total Properties</h5>
                            <h2><?php echo $total_properties['count']; ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="glass-card text-center mb-3 p-3">
                            <h5 class="card-title">Available</h5>
                            <h2 class="text-success"><?php echo $available_properties['count']; ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="glass-card text-center mb-3 p-3">
                            <h5 class="card-title">Sold</h5>
                            <h2 class="text-warning"><?php echo $sold_properties['count']; ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="glass-card text-center mb-3 p-3">
                            <h5 class="card-title">Inquiries</h5>
                            <h2 class="text-info"><?php echo $total_inquiries['count']; ?></h2>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="glass-card p-3">
                            <h5 class="mb-3 border-bottom pb-2">Recent Properties</h5>
                            <?php if (mysqli_num_rows($recent_properties) > 0): ?>
                                <table class="table table-dark table-sm">
                                    <tr>
                                        <th>Title</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                    </tr>
                                    <?php while ($prop = mysqli_fetch_assoc($recent_properties)): ?>
                                        <tr>
                                            <td><a href="property-details.php?id=<?php echo $prop['id']; ?>" class="text-white"><?php echo $prop['title']; ?></a></td>
                                            <td>$<?php echo number_format($prop['price']); ?></td>
                                            <td><span class="badge bg-<?php echo $prop['status'] == 'available' ? 'success' : 'warning'; ?>"><?php echo $prop['status']; ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                            <?php else: ?>
                                <p class="text-muted">No properties yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-3">
                            <h5 class="mb-3 border-bottom pb-2">Recent Inquiries</h5>
                            <?php if (mysqli_num_rows($recent_inquiries) > 0): ?>
                                <?php while ($inq = mysqli_fetch_assoc($recent_inquiries)): ?>
                                    <div class="border-bottom pb-2 mb-2">
                                        <strong><?php echo $inq['name']; ?></strong>
                                        <br>
                                        <small class="text-muted"><?php echo $inq['property_title']; ?></small>
                                        <br>
                                        <small><?php echo substr($inq['message'], 0, 50); ?>...</small>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-muted">No inquiries yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="add-property.php" class="btn btn-primary">+ Add New Property</a>
                    <a href="search.php" class="btn btn-secondary">Browse All Properties</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
