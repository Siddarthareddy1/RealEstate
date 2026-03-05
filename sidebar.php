<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<div class="glass-card p-3">
    <div class="list-group list-group-flush">
        <a href="<?php echo BASE_URL; ?>dashboard.php" class="list-group-item list-group-item-action <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
        <a href="<?php echo BASE_URL; ?>properties.php" class="list-group-item list-group-item-action <?php echo $current_page == 'properties' ? 'active' : ''; ?>">
            <i class="bi bi-building me-2"></i>My Properties
        </a>
        <a href="<?php echo BASE_URL; ?>add-property.php" class="list-group-item list-group-item-action <?php echo $current_page == 'add-property' ? 'active' : ''; ?>">
            <i class="bi bi-plus-circle me-2"></i>Add Property
        </a>
        <a href="<?php echo BASE_URL; ?>inquiries.php" class="list-group-item list-group-item-action <?php echo $current_page == 'inquiries' ? 'active' : ''; ?>">
            <i class="bi bi-chat-dots me-2"></i>Inquiries
        </a>
        <a href="<?php echo BASE_URL; ?>search.php" class="list-group-item list-group-item-action">
            <i class="bi bi-search me-2"></i>Browse Properties
        </a>
        <?php if (isAdmin()): ?>
            <hr style="opacity: 0.15;">
            <a href="<?php echo BASE_URL; ?>admin/" class="list-group-item list-group-item-action">
                <i class="bi bi-gear me-2"></i>Admin Panel
            </a>
        <?php endif; ?>
    </div>
</div>
