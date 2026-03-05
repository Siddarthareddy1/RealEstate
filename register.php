<?php
include "config.php";

if (isLoggedIn()) {
    redirect("dashboard.php");
}

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $phone = sanitize($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = isset($_POST['role']) && in_array($_POST['role'], ['user', 'agent']) ? $_POST['role'] : 'user';
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, phone, password, role) VALUES ('$name', '$email', '$phone', '$hashed_password', '$role')";
            
            if (mysqli_query($conn, $sql)) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DreamHome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 0;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            max-width: 520px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%);
            padding: 50px 30px 40px;
            text-align: center;
            color: white;
        }
        .register-body {
            padding: 40px 35px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="bi bi-person-plus" style="font-size: 60px; margin-bottom: 20px; display: block;"></i>
            <h2 style="font-weight: 700; margin: 0; font-size: 32px;">Create Account</h2>
            <p style="margin: 10px 0 0; opacity: 0.9; font-size: 15px;">Join DreamHome today</p>
        </div>
        <div class="register-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <div class="input-group">
                        <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <div class="input-group">
                        <input type="text" name="phone" class="form-control" placeholder="Enter phone number">
                        <span class="input-group-text"><i class="bi bi-phone"></i></span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Register As</label>
                    <select name="role" class="form-select">
                        <option value="user">Buyer/Tenant</option>
                        <option value="agent">Agent</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" class="form-control" placeholder="Create password" required>
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    </div>
                </div>
                <button type="submit" name="register" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-2"></i>Create Account
                </button>
            </form>
            <p class="text-center mt-4" style="color: #a1a1aa; font-size: 15px;">
                Already have an account? <a href="login.php" style="color: #818cf8; font-weight: 600;">Sign in</a>
            </p>
        </div>
    </div>
</body>
</html>
