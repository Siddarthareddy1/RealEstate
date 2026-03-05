<?php
include "config.php";

if (isset($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    if ($user_id != $_SESSION['user_id']) {
        mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
        $_SESSION['success'] = "User deleted!";
    }
    redirect("users.php");
}

if (isset($_GET['toggle_status'])) {
    $user_id = (int)$_GET['toggle_status'];
    $result = mysqli_query($conn, "SELECT status FROM users WHERE id = $user_id");
    $user = mysqli_fetch_assoc($result);
    $new_status = $user['status'] == 'active' ? 'inactive' : 'active';
    mysqli_query($conn, "UPDATE users SET status = '$new_status' WHERE id = $user_id");
    redirect("users.php");
}

if (isset($_GET['change_role'])) {
    $user_id = (int)$_GET['change_role'];
    $role = sanitize($_GET['role']);
    if (in_array($role, ['user', 'agent', 'admin'])) {
        mysqli_query($conn, "UPDATE users SET role = '$role' WHERE id = $user_id");
    }
    redirect("users.php");
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - DreamHome Admin</title>
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
                    <a class="nav-link active" href="users.php">
                        <i class="bi bi-people"></i>Users
                    </a>
                    <a class="nav-link" href="properties.php">
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
                <h3 class="mb-4"><i class="bi bi-people me-2"></i>Manage Users</h3>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php endif; ?>
                
                <div class="glass-card p-4">
                    <div class="table-responsive">
                        <table class="table table-dark">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($users)): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo $user['name']; ?></td>
                                        <td><?php echo $user['email']; ?></td>
                                        <td><?php echo $user['phone']; ?></td>
                                        <td>
                                            <select onchange="window.location.href='?change_role=<?php echo $user['id']; ?>&role='+this.value" class="form-select form-select-sm" style="background: var(--glass-bg); border: 1px solid var(--glass-border);">
                                                <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                                                <option value="agent" <?php echo $user['role'] == 'agent' ? 'selected' : ''; ?>>Agent</option>
                                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: <?php echo $user['status'] == 'active' ? 'linear-gradient(135deg, #10b981, #059669)' : 'linear-gradient(135deg, #ef4444, #dc2626)'; ?>">
                                                <?php echo $user['status']; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <a href="?toggle_status=<?php echo $user['id']; ?>" class="btn btn-sm" style="background: <?php echo $user['status'] == 'active' ? 'linear-gradient(135deg, #f59e0b, #d97706)' : 'linear-gradient(135deg, #10b981, #059669)'; ?>;">
                                                <?php echo $user['status'] == 'active' ? '<i class="bi bi-x-circle"></i>' : '<i class="bi bi-check-circle"></i>'; ?>
                                            </a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" style="background: linear-gradient(135deg, #ef4444, #dc2626);" onclick="return confirm('Delete this user?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            <?php endif; ?>
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
