<?php
include "config.php";

if (isset($_GET['delete'])) {
    $property_id = (int)$_GET['delete'];
    $images = mysqli_query($conn, "SELECT image_name FROM property_images WHERE property_id = $property_id");
    while ($img = mysqli_fetch_assoc($images)) {
        $file = UPLOAD_DIR . $img['image_name'];
        if (file_exists($file)) unlink($file);
    }
    mysqli_query($conn, "DELETE FROM properties WHERE id = $property_id");
    $_SESSION['success'] = "Property deleted!";
    redirect("properties.php");
}

if (isset($_GET['approve'])) {
    $property_id = (int)$_GET['approve'];
    mysqli_query($conn, "UPDATE properties SET is_approved = 1 WHERE id = $property_id");
    $_SESSION['success'] = "Property approved!";
    redirect("properties.php");
}

if (isset($_GET['toggle_featured'])) {
    $property_id = (int)$_GET['toggle_featured'];
    $result = mysqli_query($conn, "SELECT is_featured FROM properties WHERE id = $property_id");
    $prop = mysqli_fetch_assoc($result);
    $new = $prop['is_featured'] ? 0 : 1;
    mysqli_query($conn, "UPDATE properties SET is_featured = $new WHERE id = $property_id");
    redirect("properties.php");
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$where = "1=1";
if ($filter == 'pending') $where = "is_approved = 0";
elseif ($filter == 'approved') $where = "is_approved = 1";
elseif ($filter == 'featured') $where = "is_featured = 1";

$properties = mysqli_query($conn, "SELECT p.*, u.name as owner_name, u.email as owner_email 
                                   FROM properties p 
                                   JOIN users u ON p.user_id = u.id 
                                   WHERE $where 
                                   ORDER BY p.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Properties - DreamHome Admin</title>
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
        .nav-tabs .nav-link {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            margin-right: 5px;
            border-radius: 10px 10px 0 0;
        }
        .nav-tabs .nav-link.active {
            background: var(--glass-bg);
            border-color: var(--glass-border);
            color: var(--text-primary);
            border-bottom-color: transparent;
        }
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
                    <a class="nav-link active" href="properties.php">
                        <i class="bi bi-building"></i>Properties
                    </a>
                    <a class="nav-link" href="inquiries.php">
                        <i class="bi bi-chat-dots"></i>Inquiries
                    </a>
                    <a class="nav-link" href="../logout.php">
                        <i class="bi bi-box-arrow-right"></i>Logout
                    </a>
                </nav>
            </div>
            <div class="col-md-10 p-4">
                <h3 class="mb-4"><i class="bi bi-building me-2"></i>Manage Properties</h3>
                
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $filter == 'all' ? 'active' : ''; ?>" href="?filter=all">All</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $filter == 'pending' ? 'active' : ''; ?>" href="?filter=pending">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $filter == 'approved' ? 'active' : ''; ?>" href="?filter=approved">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $filter == 'featured' ? 'active' : ''; ?>" href="?filter=featured">Featured</a>
                    </li>
                </ul>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <div class="glass-card p-4">
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Owner</th>
                                    <th>Price</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Approved</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($prop = mysqli_fetch_assoc($properties)): ?>
                                    <tr>
                                        <td><?php echo $prop['id']; ?></td>
                                        <td><?php echo $prop['title']; ?></td>
                                        <td><?php echo $prop['owner_name']; ?></td>
                                        <td>$<?php echo number_format($prop['price']); ?></td>
                                        <td><?php echo ucfirst($prop['property_type']); ?></td>
                                        <td><?php echo $prop['status']; ?></td>
                                        <td>
                                            <span class="badge" style="background: <?php echo $prop['is_approved'] ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #f59e0b, #d97706)'; ?>">
                                                <?php echo $prop['is_approved'] ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: <?php echo $prop['is_featured'] ? 'linear-gradient(135deg, #6366f1, #8b5cf6)' : 'linear-gradient(135deg, #6b7280, #4b5563)'; ?>">
                                                <?php echo $prop['is_featured'] ? 'Yes' : 'No'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!$prop['is_approved']): ?>
                                                <a href="?approve=<?php echo $prop['id']; ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
                                                    <i class="bi bi-check"></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="?toggle_featured=<?php echo $prop['id']; ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                                                <i class="bi bi-star<?php echo $prop['is_featured'] ? '-fill' : ''; ?>"></i>
                                            </a>
                                            <a href="../property-details.php?id=<?php echo $prop['id']; ?>" target="_blank" class="btn btn-sm" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="?delete=<?php echo $prop['id']; ?>" class="btn btn-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626);" onclick="return confirm('Delete this property?')">
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
