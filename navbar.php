<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>">
            <i class="bi bi-house-heart-fill me-2"></i>DreamHome
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'index' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>">
                        <i class="bi bi-house me-1"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'search' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>search.php">
                        <i class="bi bi-building me-1"></i>Properties
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'contact' ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>contact.php">
                        <i class="bi bi-envelope me-1"></i>Contact
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i><?php echo $_SESSION['name']; ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>dashboard.php">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>properties.php">
                                <i class="bi bi-building me-2"></i>My Properties
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>inquiries.php">
                                <i class="bi bi-chat-dots me-2"></i>Inquiries
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>login.php">
                            <i class="bi bi-box-arrow-in-right me-1"></i>Login
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>register.php">
                            <i class="bi bi-person-plus me-1"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--dark-card); border: 1px solid var(--glass-border); border-radius: 20px;">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-box-arrow-right" style="font-size: 60px; background: var(--gradient-primary); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                </div>
                <h3 class="mb-3">Ready to Leave?</h3>
                <p class="text-muted mb-4">Are you sure you want to logout from your account?</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding: 12px 30px; border-radius: 10px;">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                    <a href="<?php echo BASE_URL; ?>logout.php" class="btn btn-primary" style="padding: 12px 30px; border-radius: 10px;">
                        <i class="bi bi-check-circle me-2"></i>Yes, Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
