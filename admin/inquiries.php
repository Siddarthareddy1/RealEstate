<?php
include "config.php";

if (isset($_GET['delete'])) {
    $inquiry_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM inquiries WHERE id = $inquiry_id");
    $_SESSION['success'] = "Inquiry deleted!";
    redirect("inquiries.php");
}

$inquiries = mysqli_query($conn, "SELECT i.*, p.title as property_title, u.name as user_name 
                                   FROM inquiries i 
                                   LEFT JOIN properties p ON i.property_id = p.id 
                                   LEFT JOIN users u ON i.user_id = u.id 
                                   ORDER BY i.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - DreamHome Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        body { background: var(--gradient-dark); }
        .sidebar {
            min-height: 100vh;
            background: rgba(10, 10, 15, 0.95);
            border-right: 1px solid var(--glass-border);
        }
        .sidebar .nav-link {
            color: var(--text-secondary);
            padding: 15px 20px;
            border-radius: 10px;
            margin: 5px 10px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: var(--glass-bg);
            color: var(--text-primary);
        }
        .sidebar .nav-link i { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar p-0">
                <div class="p-3 text-center border-bottom" style="border-color: var(--glass-border) !important;">
                    <h5 class="mb-0"><i class="bi bi-house-heart-fill me-2 text-primary"></i>DreamHome</h5>
                    <small class="text-muted">Admin Panel</small>
                </div>
                <nav class="nav flex-column mt-3">
                    <a class="nav-link" href="index.php">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="bi bi-people"></i>Users
                    </a>
                    <a class="nav-link" href="properties.php">
                        <i class="bi bi-building"></i>Properties
                    </a>
                    <a class="nav-link active" href="inquiries.php">
                        <i class="bi bi-chat-dots"></i>Inquiries
                    </a>
                    <a class="nav-link" href="../logout.php">
                        <i class="bi bi-box-arrow-right"></i>Logout
                    </a>
                </nav>
            </div>
            <div class="col-md-10 p-4">
                <h3 class="mb-4"><i class="bi bi-chat-dots me-2"></i>Manage Inquiries</h3>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <div class="glass-card p-4">
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Property</th>
                                    <th>User</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Message</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($inq = mysqli_fetch_assoc($inquiries)): ?>
                                    <tr>
                                        <td><?php echo $inq['id']; ?></td>
                                        <td><?php echo $inq['property_id'] > 0 ? $inq['property_title'] : 'General'; ?></td>
                                        <td><?php echo $inq['user_name'] ?: 'Guest'; ?></td>
                                        <td><?php echo $inq['name']; ?></td>
                                        <td><?php echo $inq['email']; ?></td>
                                        <td><?php echo $inq['phone']; ?></td>
                                        <td><?php echo substr($inq['message'], 0, 30); ?>...</td>
                                        <td><?php echo date('d M Y', strtotime($inq['created_at'])); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo $inq['email']; ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                                <i class="bi bi-reply"></i>
                                            </a>
                                            <a href="?delete=<?php echo $inq['id']; ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626);" onclick="return confirm('Delete?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
