<?php
session_start();
include "db.php";
include "navbar.php";

// Fetch gyms and sort by city
$sql = "SELECT * FROM gym ORDER BY SUBSTRING_INDEX(gym_address, ' ', 1) ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Database error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gyms - Gym Management</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="container">
<h2>Available Gyms</h2>
<div class="gym-list-wrapper">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="gym-card">
            <h3 class="gym-name"><?php echo htmlspecialchars($row['gym_name']); ?></h3>
            <p class="gym-type"><?php echo htmlspecialchars($row['gym_type']); ?></p>
            <p class="gym-address"><?php echo htmlspecialchars($row['gym_address']); ?></p>

            <a href="view_gym.php?gym_id=<?php echo $row['gym_id']; ?>" class="btn">View Gym</a>
        </div>
    <?php endwhile; ?>
</div>
</div>

</body>
</html>
