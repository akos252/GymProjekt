<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Gym Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Gym Management System</h1>
        <p>Manage your gym members, trainers, and more with ease.</p>
        
        <?php if (isset($_SESSION['username'])): ?>
            <p>You are logged in as <strong><?php echo $_SESSION['username']; ?></strong>.</p>
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
            <a href="logout.php" class="btn">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn">Login</a>
        <?php endif; ?>
    </div>
</body>
</html>