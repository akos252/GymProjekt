<!-- Navigation Bar -->
<nav class="navbar"> 
  <div class="nav-left">
    <a href="index.php">
      <img src="assets/logo.png" alt="GMS Logo" class="logo">
    </a>
  </div>

  <div class="nav-center">
    <a href="gyms.php">View Gyms</a>
    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'owner'): ?>
      <a href="owner_dashboard.php" style="color: red;">Owner Dashboard</a>
    <?php endif; ?>
  </div>

  <div class="nav-right">
    <?php if (isset($_SESSION['username'])): ?>
      <a href="profile.php" class="btn">Profile</a>
    <?php else: ?>
      <a href="login.php" class="btn">Login</a>
    <?php endif; ?>
  </div>
</nav>