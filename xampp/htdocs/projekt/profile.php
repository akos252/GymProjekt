<?php
session_start();
include "db.php";
include "navbar.php";

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user's gyms
$sql = "SELECT g.gym_id, g.gym_name, g.gym_address, g.gym_type 
        FROM gym g
        JOIN memberships m ON g.gym_id = m.gym_id
        JOIN login l ON m.user_id = l.id
        WHERE l.username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - Gym Management</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div style="max-width: 900px; margin: auto; padding: 20px;" class="container">
    <h2>Profile</h2>
    <h2>Logged in as <strong><?php echo htmlspecialchars($username); ?></strong></h2>

    <h3>My memberships</h3>

    <?php if ($result->num_rows > 0): ?>
        <div style="display: flex; flex-wrap: wrap; justify-content: space-around; gap: 20px;">
            <?php while ($gym = $result->fetch_assoc()): ?>
                <div style="background: #1e1e1e; padding: 20px; border-radius: 10px; width: 40%; text-align: center;">
                    <h3 style="color: red; font-size: 20px;"><?php echo htmlspecialchars($gym['gym_name']); ?></h3>
                    <p style="font-weight: bold; margin-top: 10px;"><?php echo htmlspecialchars($gym['gym_type']); ?></p>
                    <p style="color: #bbb;"><?php echo htmlspecialchars($gym['gym_address']); ?></p>

                    <a href="view_gym.php?gym_id=<?php echo $gym['gym_id']; ?>" 
                       style="display: inline-block; margin-top: 15px; background: red; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
                        View Gym
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>You are not a member of any gyms.</p>
    <?php endif; ?>

    <br>
    <a href="logout.php" class="btn">Logout</a>
</div>

</body>
</html>