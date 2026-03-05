<?php
include "config.php";

if (!isLoggedIn()) {
    redirect("login.php");
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['delete'])) {
    $inquiry_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM inquiries WHERE id = $inquiry_id");
    $_SESSION['success'] = "Inquiry deleted!";
    redirect("inquiries.php");
}

if (isset($_GET['reply'])) {
    $inquiry_id = (int)$_GET['reply'];
    mysqli_query($conn, "UPDATE inquiries SET status = 'replied' WHERE id = $inquiry_id");
    $_SESSION['success'] = "Marked as replied!";
    redirect("inquiries.php");
}

$inquiries = mysqli_query($conn, "SELECT i.*, p.title as property_title, p.id as property_id 
                                   FROM inquiries i 
                                   JOIN properties p ON i.property_id = p.id 
                                   WHERE p.user_id = $user_id 
                                   ORDER BY i.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inquiries - Real Estate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .table-dark-custom {
            --bs-table-bg: rgba(30, 30, 40, 0.7);
            --bs-table-color: #fff;
        }
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
                <h3>My Inquiries</h3>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <?php if (mysqli_num_rows($inquiries) > 0): ?>
                    <div class="glass-card p-3">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Property</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Message</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($inq = mysqli_fetch_assoc($inquiries)): ?>
                                        <tr>
                                            <td><a href="property-details.php?id=<?php echo $inq['property_id']; ?>" class="text-white"><?php echo $inq['property_title']; ?></a></td>
                                            <td><?php echo $inq['name']; ?></td>
                                            <td><?php echo $inq['email']; ?></td>
                                            <td><?php echo $inq['phone']; ?></td>
                                            <td><?php echo substr($inq['message'], 0, 50); ?>...</td>
                                            <td>
                                                <span class="badge bg-<?php echo $inq['status'] == 'pending' ? 'warning' : ($inq['status'] == 'replied' ? 'success' : 'secondary'); ?>">
                                                    <?php echo ucfirst($inq['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d M Y', strtotime($inq['created_at'])); ?></td>
                                            <td>
                                                <a href="mailto:<?php echo $inq['email']; ?>" class="btn btn-sm btn-primary">Reply</a>
                                                <?php if ($inq['status'] == 'pending'): ?>
                                                    <a href="?reply=<?php echo $inq['id']; ?>" class="btn btn-sm btn-success">Mark Replied</a>
                                                <?php endif; ?>
                                                <a href="?delete=<?php echo $inq['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this inquiry?')">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="glass-card p-4 text-center">
                        <p class="text-muted mb-0">No inquiries yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
