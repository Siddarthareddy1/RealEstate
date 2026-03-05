<?php
include "config.php";

if (isLoggedIn()) {
    redirect("dashboard.php");
}

$error = "";
$loginMethod = isset($_POST['login_method']) ? $_POST['login_method'] : 'email';

$google_client_id = "YOUR_GOOGLE_CLIENT_ID";
$google_client_secret = "YOUR_GOOGLE_CLIENT_SECRET";
$google_configured = ($google_client_id !== "YOUR_GOOGLE_CLIENT_ID" && !empty($google_client_id));

if (isset($_POST['login'])) {
    $loginMethod = $_POST['login_method'];
    
    if ($loginMethod === 'email') {
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND status = 'active'");
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    redirect("admin/index.php");
                } elseif ($user['role'] === 'agent') {
                    redirect("dashboard.php");
                } else {
                    redirect("dashboard.php");
                }
            } else {
                $error = "Invalid password!";
            }
        } else {
            $error = "Email not found or account inactive!";
        }
    } elseif ($loginMethod === 'mobile') {
        $phone = sanitize($_POST['phone']);
        $otp = sanitize($_POST['otp']);
        
        if (!isset($_SESSION['mobile_otp']) || !isset($_SESSION['mobile_login_phone'])) {
            $error = "Please request OTP first!";
        } elseif ($_SESSION['mobile_otp'] == $otp && $_SESSION['mobile_login_phone'] == $phone) {
            $result = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone' AND status = 'active'");
            
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                unset($_SESSION['mobile_otp']);
                unset($_SESSION['mobile_login_phone']);
                
                if ($user['role'] === 'admin') {
                    redirect("admin/index.php");
                } elseif ($user['role'] === 'agent') {
                    redirect("dashboard.php");
                } else {
                    redirect("dashboard.php");
                }
            } else {
                $error = "Mobile number not found or account inactive!";
            }
        } else {
            $error = "Invalid OTP!";
        }
    } elseif ($loginMethod === 'google_demo') {
        $google_email = sanitize($_POST['google_email']);
        
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$google_email' AND status = 'active'");
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] === 'admin') {
                redirect("admin/index.php");
            } elseif ($user['role'] === 'agent') {
                redirect("dashboard.php");
            } else {
                redirect("dashboard.php");
            }
        } else {
            $error = "No account found with this Google email. Please register first.";
        }
    }
}

if (isset($_POST['send_otp'])) {
    $phone = sanitize($_POST['phone']);
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone' AND status = 'active'");
    
    if (mysqli_num_rows($result) > 0) {
        $otp = rand(100000, 999999);
        $_SESSION['mobile_otp'] = $otp;
        $_SESSION['mobile_login_phone'] = $phone;
        
        $message = "Your Real Estate login OTP is: $otp";
        $sms_result = sendSMS($phone, $message);
        
        if (isset($sms_result['status']) && $sms_result['status'] === 'demo') {
            $error = "OTP sent to your mobile! (Demo: OTP is $otp)";
        } elseif (isset($sms_result['sid'])) {
            $error = "OTP sent to your mobile number!";
        } else {
            $error = "Failed to send OTP. Please try again.";
        }
    } else {
        $error = "Mobile number not found or account inactive!";
    }
}

if (isset($_GET['code']) && isset($_GET['google_login']) && $google_configured) {
    $redirect_uri = BASE_URL . "login.php?google_login=1";
    
    $token_url = "https://oauth2.googleapis.com/token";
    $token_data = [
        'code' => $_GET['code'],
        'client_id' => $google_client_id,
        'client_secret' => $google_client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($token_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $token = json_decode($response, true);
    
    if (isset($token['access_token'])) {
        $userinfo_url = "https://www.googleapis.com/oauth2/v2/userinfo";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $userinfo_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer " . $token['access_token']]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userinfo = json_decode(curl_exec($ch), true);
        curl_close($ch);
        
        if (isset($userinfo['email'])) {
            $email = sanitize($userinfo['email']);
            $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND status = 'active'");
            
            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                if ($user['role'] === 'admin') {
                    redirect("admin/index.php");
                } elseif ($user['role'] === 'agent') {
                    redirect("dashboard.php");
                } else {
                    redirect("dashboard.php");
                }
            } else {
                $error = "No account found with this Google email. Please register first.";
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
    <title>Login - DreamHome</title>
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
        .login-container {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 24px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            max-width: 480px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #06b6d4 100%);
            padding: 50px 30px 40px;
            text-align: center;
            color: white;
        }
        .login-header i {
            font-size: 60px;
            margin-bottom: 20px;
            display: block;
        }
        .login-header h2 {
            font-weight: 700;
            margin: 0;
            font-size: 32px;
        }
        .login-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-size: 15px;
        }
        .login-body {
            padding: 40px 35px;
        }
        .form-label {
            font-weight: 500;
            color: #a1a1aa;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.5);
        }
        .social-btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            color: #e4e4e7;
            font-weight: 500;
            transition: all 0.3s;
        }
        .social-btn:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }
        .google-icon {
            width: 22px;
            height: 22px;
        }
        .divider-text {
            display: flex;
            align-items: center;
            margin: 28px 0;
        }
        .divider-text::before, .divider-text::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.15);
        }
        .divider-text span {
            padding: 0 18px;
            color: #71717a;
            font-size: 13px;
        }
        .register-link {
            text-align: center;
            margin-top: 28px;
            color: #a1a1aa;
            font-size: 15px;
        }
        .register-link a {
            color: #818cf8;
            font-weight: 600;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 12px;
            font-size: 14px;
        }
        .input-group-text {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-left: none;
            border-radius: 0 12px 12px 0;
            color: #71717a;
        }
        .form-control.with-icon {
            border-right: none;
            border-radius: 12px 0 0 12px;
        }
        .toggle-password {
            cursor: pointer;
            color: #71717a;
        }
        .toggle-password:hover {
            color: #a1a1aa;
        }
        .login-method-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
        }
        .method-tab {
            flex: 1;
            padding: 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            color: #71717a;
        }
        .method-tab.active {
            border-color: #6366f1;
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
        }
        .method-tab:hover:not(.active) {
            border-color: rgba(255, 255, 255, 0.25);
            background: rgba(255, 255, 255, 0.1);
        }
        .method-tab i {
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="bi bi-house-heart"></i>
            <h2>Welcome Back</h2>
            <p>Sign in to find your dream property</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($google_configured): ?>
            <a href="https://accounts.google.com/o/oauth2/v2/auth?client_id=<?php echo $google_client_id; ?>&redirect_uri=<?php echo urlencode(BASE_URL . 'login.php?google_login=1'); ?>&response_type=code&scope=email%20profile&access_type=offline" class="social-btn">
                <img src="https://www.google.com/favicon.ico" class="google-icon" alt="Google">
                Continue with Google
            </a>
            <div class="divider-text"><span>or</span></div>
            <?php else: ?>
            <form method="POST" class="mb-3">
                <input type="hidden" name="login_method" value="google_demo">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="google_email" class="form-control" placeholder="Enter your email" value="admin@realestate.com">
                </div>
                <button type="submit" name="login" class="social-btn w-100">
                    <img src="https://www.google.com/favicon.ico" class="google-icon" alt="Google">
                    Sign in with Google
                </button>
            </form>
            <div class="divider-text"><span>or</span></div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <input type="hidden" name="login_method" id="loginMethod" value="email">
                
                <div class="login-method-tabs">
                    <div class="method-tab active" onclick="setLoginMethod('email', this)">
                        <i class="bi bi-envelope"></i>Email
                    </div>
                    <div class="method-tab" onclick="setLoginMethod('mobile', this)">
                        <i class="bi bi-phone"></i>Mobile
                    </div>
                </div>
                
                <div id="emailFields">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-group">
                            <input type="email" name="email" class="form-control" id="emailInput" placeholder="Enter your email">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Enter your password">
                            <span class="input-group-text toggle-password" onclick="togglePassword()"><i class="bi bi-eye-slash" id="passwordIcon"></i></span>
                        </div>
                    </div>
                </div>
                
                <div id="mobileFields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Mobile Number</label>
                        <div class="input-group">
                            <input type="tel" name="phone" class="form-control" id="phoneInput" placeholder="Enter mobile number">
                            <span class="input-group-text"><i class="bi bi-phone"></i></span>
                        </div>
                    </div>
                    <div class="mb-3" id="otpField" style="display: none;">
                        <label class="form-label">One Time Password</label>
                        <div class="input-group">
                            <input type="text" name="otp" class="form-control" placeholder="Enter OTP">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="login" class="btn btn-primary btn-login w-100" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                </button>
            </form>
            
            <div class="register-link">
                Don't have an account? <a href="register.php">Create one</a>
            </div>
        </div>
    </div>
    
    <script>
        function setLoginMethod(method, tab) {
            document.querySelectorAll('.method-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const emailFields = document.getElementById('emailFields');
            const mobileFields = document.getElementById('mobileFields');
            const loginMethod = document.getElementById('loginMethod');
            const loginBtn = document.getElementById('loginBtn');
            const otpField = document.getElementById('otpField');
            
            if (method === 'email') {
                emailFields.style.display = 'block';
                mobileFields.style.display = 'none';
                loginMethod.value = 'email';
                loginBtn.name = 'login';
                loginBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Sign In';
                otpField.style.display = 'none';
            } else {
                emailFields.style.display = 'none';
                mobileFields.style.display = 'block';
                loginMethod.value = 'mobile';
                loginBtn.name = 'send_otp';
                loginBtn.innerHTML = '<i class="bi bi-send me-2"></i>Send OTP';
                otpField.style.display = 'none';
            }
        }
        
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const passwordIcon = document.getElementById('passwordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            }
        }
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginType = document.querySelector('.method-tab.active').textContent.trim();
            const loginBtn = document.getElementById('loginBtn');
            const otpField = document.getElementById('otpField');
            
            if (loginType === 'Mobile' && loginBtn.name === 'send_otp') {
                return;
            } else if (loginType === 'Mobile' && otpField.style.display === 'none') {
                e.preventDefault();
                otpField.style.display = 'block';
                loginBtn.name = 'login';
                loginBtn.innerHTML = '<i class="bi bi-box-arrow-in-right me-2"></i>Login with OTP';
            }
        });
    </script>
</body>
</html>
