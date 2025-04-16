<?php
session_start();
include "db.php";

// Ensure user is logged in and `id` is set
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to purchase a membership.");
}

// Get gym_id from URL
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // ✅ Uses `id` from `login` table
    $name = trim($_POST['name']);
    $duration = intval($_POST['membership_duration']);

    // Ensure `user_id` is not empty
    if (empty($user_id)) {
        die("Error: user_id is missing from session.");
    }

    // Calculate membership dates
    $start_date = date("Y-m-d");
    $end_date = date("Y-m-d", strtotime("+$duration days"));

    // Insert into memberships table
    $sql = "INSERT INTO memberships (user_id, gym_id, name, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL Error (Prepare Failed): " . $conn->error);
    }

    $stmt->bind_param("issss", $user_id, $gym_id, $name, $start_date, $end_date);
    
    if ($stmt->execute()) {
        echo "<script>alert('Membership purchased successfully!'); window.location.href='gyms.php';</script>";
    } else {
        die("SQL Error (Execution Failed): " . $stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Membership</title>
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

<div class="container">
    <h2>Purchase Membership for <?php echo htmlspecialchars($gym['gym_name']); ?></h2>
    
    <form method="post">
        <input type="text" name="name" placeholder="Your Name" required><br>
        
        <select name="membership_duration" required>
            <option value="7">7 Days</option>
            <option value="30">30 Days</option>
            <option value="90">90 Days</option>
        </select><br>

        <h3>Enter Card Details</h3>
        <input type="text" name="card_number" placeholder="Card Number" required><br>
        <input type="text" name="expiry" placeholder="Expiry Date (MM/YY)" required><br>
        <input type="text" name="cvv" placeholder="CVV" required><br>

        <button class="btn" type="submit">Purchase Membership</button>
    </form>
</div>

</body>
</html>