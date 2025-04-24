<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar"> 
  <div class="nav-left">
    <div class="hamburger" onclick="toggleMenu()">☰</div>
    <a href="index.php">
      <img src="asstets/logo.png" alt="GMS Logo" class="logo">
    </a>
  </div>

  <div class="nav-center" id="nav-links">
    <a href="gyms.php">View Gyms</a>
    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'owner'): ?>
      <a href="owner_dashboard.php" style="color: red;">Owner Dashboard</a>
    <?php endif; ?>
    <?php if (isset($_SESSION['username'])): ?>
      <a href="profile.php" class="btn">Profile</a>
      <a href="logout.php" class="btn">Logout</a>
    <?php else: ?>
      <a href="login.php" class="btn">Login</a>
    <?php endif; ?>
  </div>
</nav>


<!-- Mobile dropdown links -->
<div class="mobile-menu" id="mobile-menu">
  <a href="gyms.php">View Gyms</a>
  <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'owner'): ?>
    <a href="owner_dashboard.php">Owner Dashboard</a>
  <?php endif; ?>
</div>

<!-- Main Content -->
<div class="container">
    <h1>Welcome to Gym Management System</h1>
    <p>For Gym Owners, trainers and Fitness enthusiast</p>

    <?php if (isset($_SESSION['username'])): ?>
        <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>.</p>
        <a href="logout.php" class="btn">Logout</a>
    <?php else: ?>
        <p><a href="login.php" class="btn">Login</a> to access the system.</p>
    <?php endif; ?>
    <p style="margin: 20px; padding: 20px; font-size: 20px">This is GMS – Gym Management System, your all-in-one platform for managing gyms, memberships, and trainers with ease. Whether you're a gym owner or a fitness enthusiast, GMS helps streamline scheduling, communication, and member tracking. Simple, efficient, and built to power your fitness business.</p><p class="contact-info">
    <span>📞 Phone:<br> +36 30 889 2512</span>
    <span>📧 Email:<br> gymmsystem.info@gmail.com</span>
</p>
</p>
</div>
<script>
function toggleMenu() {
  const nav = document.getElementById("nav-links");
  nav.classList.toggle("show");
}
</script>


</body>
</html>
