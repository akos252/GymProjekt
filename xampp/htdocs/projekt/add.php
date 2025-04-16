<?php
session_start();
include "db.php"; // Ensure this connects to your database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $gym_id = uniqid("GYM"); // Generates a unique gym ID
    $gym_name = trim($_POST['gym_name']);
    $gym_address = trim($_POST['gym_address']);
    $gym_type = trim($_POST['gym_type']);

    // Check if the gym already exists
    $check_gym = "SELECT * FROM gym WHERE gym_name = ?";
    $stmt = $conn->prepare($check_gym);
    $stmt->bind_param("s", $gym_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "A gym with this name already exists!";
    } else {
        // Insert new gym
        $insert_gym = "INSERT INTO gym (gym_id, gym_name, gym_address, gym_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_gym);
        $stmt->bind_param("ssss", $gym_id, $gym_name, $gym_address, $gym_type);
        if ($stmt->execute()) {
            $success = "Gym added successfully!";
        } else {
            $error = "Error adding gym. Try again!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a Gym</title>
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
            <a href="#">Placeholder</a>
            <a href="#">Placeholder</a>
        </div>
        <div class="nav-right">
            <a href="profile.php" class="btn"><?php echo "Profile"; ?></a>
        </div>
    </nav>

    <div class="container">
        <h2>Add a Gym</h2>
        <form method="post">
            <input type="text" name="gym_name" placeholder="Gym Name" required><br>
            <input type="text" name="gym_address" placeholder="Gym Address" required><br>
            <input type="text" name="gym_type" placeholder="Gym Type (e.g., CrossFit, Bodybuilding)" required><br>
            <button type="submit">Add Gym</button>
        </form>
        
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <?php if (isset($success)) { echo "<p style='color:green;'>$success</p>"; } ?>

        <br>
        <a href="index.php" class="btn">Back to Home</a>
    </div>

</body>
</html>