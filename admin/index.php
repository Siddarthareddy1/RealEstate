<?php
include "config.php";

$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"));
$total_agents = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'agent'"));
$total_properties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM properties"));
$pending_properties = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM properties WHERE is_approved = 0"));
$total_inquiries = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM inquiries"));

$recent_users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
$recent_properties = mysqli_query($conn, "SELECT p.*, u.name as owner_name FROM properties p JOIN users u ON p.user_id = u.id ORDER BY p.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DreamHome</title>
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
                    <a class="nav-link active" href="index.php">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="users.php">
                        <i class="bi bi-people"></i>Users
                    </a>
                    <a class="nav-link" href="properties.php">
                        <i class="bi bi-building"></i>Properties
                    </a>
                    <a class="nav-link" href="inquiries.php">
                        <i class="bi bi-chat-dots"></i>Inquiries
                    </a>
                    <a class="nav-link" href="#" onclick="return confirm('Are you sure you want to logout?')">
                        <i class="bi bi-box-arrow-right"></i>Logout
                    </a>
                </nav>
            </div>
            <div class="col-md-10 p-4">
                <h3 class="mb-4"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h3>
                
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h2><?php echo $total_users['count']; ?></h2>
                            <p><i class="bi bi-people"></i>Total Users</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h2><?php echo $total_agents['count']; ?></h2>
                            <p><i class="bi bi-person-badge"></i>Agents</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card">
                            <h2><?php echo $total_properties['count']; ?></h2>
                            <p><i class="bi bi-building"></i>Properties</p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-4 mt-2">
                    <div class="col-md-6">
                        <div class="glass-card p-4">
                            <h5 class="mb-4"><i class="bi bi-people me-2"></i>Recent Users</h5>
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = mysqli_fetch_assoc($recent_users)): ?>
                                        <tr>
                                            <td><?php echo $user['name']; ?></td>
                                            <td><?php echo $user['email']; ?></td>
                                            <td><?php echo ucfirst($user['role']); ?></td>
                                            <td><span class="badge" style="background: <?php echo $user['status'] == 'active' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)'; ?>"><?php echo $user['status']; ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <a href="users.php" class="btn btn-primary btn-sm">
                                <i class="bi bi-gear me-1"></i>Manage Users
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="glass-card p-4">
                            <h5 class="mb-4"><i class="bi bi-building me-2"></i>Recent Properties</h5>
                            <table class="table table-dark">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Owner</th>
                                        <th>Status</th>
                                        <th>Approved</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($prop = mysqli_fetch_assoc($recent_properties)): ?>
                                        <tr>
                                            <td><?php echo $prop['title']; ?></td>
                                            <td><?php echo $prop['owner_name']; ?></td>
                                            <td><?php echo $prop['status']; ?></td>
                                            <td><span class="badge" style="background: <?php echo $prop['is_approved'] ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #f59e0b, #d97706)'; ?>"><?php echo $prop['is_approved'] ? 'Yes' : 'No'; ?></span></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <a href="properties.php" class="btn btn-primary btn-sm">
                                <i class="bi bi-gear me-1"></i>Manage Properties
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <a href="users.php" class="btn btn-primary me-2">
                        <i class="bi bi-people me-2"></i>Manage Users
                    </a>
                    <a href="properties.php" class="btn btn-primary me-2">
                        <i class="bi bi-building me-2"></i>Manage Properties
                    </a>
                    <a href="inquiries.php" class="btn btn-primary">
                        <i class="bi bi-chat-dots me-2"></i>View Inquiries
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
