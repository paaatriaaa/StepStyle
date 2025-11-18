<?php
// components/admin-header.php
?>
<header class="admin-header">
    <div class="admin-header-content">
        <div class="admin-logo">
            <a href="../index.php">
                <i class="fas fa-shoe-prints"></i>
                <span>StepStyle Admin</span>
            </a>
        </div>
        
        <div class="admin-user-menu">
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['user_name'] ?? 'Admin'; ?></span>
                <div class="user-dropdown">
                    <a href="../user/profile.php"><i class="fas fa-user"></i> Profile</a>
                    <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</header>