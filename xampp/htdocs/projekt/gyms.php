<?php
session_start();
include "db.php";

// Fetch gyms and sort by city (first word in address)
$sql = "SELECT * FROM gym ORDER BY SUBSTRING_INDEX(gym_address, ' ', 1) ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gyms - Gym Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar">
    <div class="nav-left">
        <a href="index.php">
            <img src="asstets/logo.png" alt="GMS Logo" class="logo">
        </a>
    </div>
    <div class="nav-center">
        <a href="gyms.php">View Gyms</a>

        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'owner'): ?>
            <a href="owner_dashboard.php" style="color: red;">Owner Dashboard</a>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <a href="<?php echo isset($_SESSION['username']) ? 'profile.php' : 'login.php'; ?>" class="btn">
            <?php echo isset($_SESSION['username']) ? 'Profile' : 'Login'; ?>
        </a>
    </div>
</nav>

<div style="max-width: 900px; margin: auto; padding: 20px;" class="container">
    <h2>Available Gyms</h2>
    <div style="display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div style="background: #1e1e1e; padding: 20px; border-radius: 10px; width: 40%; text-align: center;">
                <h3 class="gym-name" style="color: red; font-size: 20px; transition: color 0.3s;">
                    <?php echo htmlspecialchars($row['gym_name']); ?>
                </h3>
                <p style="font-weight: bold; margin-top: 10px;"><?php echo htmlspecialchars($row['gym_type']); ?></p>
                <p style="color: #bbb;"><?php echo htmlspecialchars($row['gym_address']); ?></p>

                <!-- View Gym Button -->
                <a href="view_gym.php?gym_id=<?php echo $row['gym_id']; ?>" 
                   style="display: inline-block; margin-top: 15px; background: red; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
                    View Gym
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
