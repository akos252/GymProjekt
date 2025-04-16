<?php
session_start();
include "db.php";

// Check if gym_id is provided
if (!isset($_GET['gym_id'])) {
    die("Error: No gym selected.");
}

$gym_id = $_GET['gym_id'];

// Fetch gym details
$sql = "SELECT gym_name FROM gym WHERE gym_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $gym_id);
$stmt->execute();
$result = $stmt->get_result();
$gym = $result->fetch_assoc();

if (!$gym) {
    die("Error: Gym not found.");
}

// Fetch trainers at this gym, including images
$trainer_sql = "SELECT name, time, mobilenum, image FROM trainer WHERE trainer_id IN 
                (SELECT trainer_id FROM trainer_gym WHERE gym_id = ?)";
$trainer_stmt = $conn->prepare($trainer_sql);
$trainer_stmt->bind_param("s", $gym_id);
$trainer_stmt->execute();
$trainer_result = $trainer_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($gym['gym_name']); ?> - Gym Details</title>
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
            <a href="gyms.php">View gyms</a>
            <a href="add.php">add</a>
            <a href="add_trainer.php">addtrainer</a>
            <a href="#">Placeholder</a>
        </div>
        <div class="nav-right">
            <a href="profile.php" class="btn"><?php echo "Profile"; ?></a>
        </div>
    </nav>

<div style="max-width: 600px; margin: auto; padding: 20px; background: #1e1e1e; border-radius: 10px;">
    <h2 style="color: red;"><?php echo htmlspecialchars($gym['gym_name']); ?></h2>
    
    <h3>Available Personal Trainers</h3>
    <?php if ($trainer_result->num_rows > 0): ?>
        <ul style="list-style: none; padding: 0;">
            <?php while ($trainer = $trainer_result->fetch_assoc()): ?>
                <li style="background: #333; padding: 10px; margin: 5px; border-radius: 5px; text-align: center;">
                    <strong><?php echo htmlspecialchars($trainer['name']); ?></strong><br>
                    Time: <?php echo htmlspecialchars($trainer['time']); ?><br>
                    Contact: <?php echo htmlspecialchars($trainer['mobilenum']); ?><br>

                    <!-- Display trainer image (smaller size, keep aspect ratio) -->
                    <?php if (!empty($trainer['image'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($trainer['image']); ?>" 
                             alt="Trainer Image" 
                             style="max-width: 270px; height: auto; display: block; margin: auto; border-radius: 10px;">
                    <?php else: ?>
                        <p style="color: gray;">No image available.</p>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p style="color: gray;">No trainers available.</p>
    <?php endif; ?>

    <!-- Purchase Membership Button -->
    <a href="purchase_membership.php?gym_id=<?php echo $gym_id; ?>" 
       style="display: inline-block; margin-top: 15px; background: red; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
        Purchase Membership
    </a>
</div>

</body>
</html>