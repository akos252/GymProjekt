<?php
session_start();
include "db.php";
include "navbar.php";
require_once "includes/membership_functions.php";

// Check if user is logged in
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id'])) {
    die("Error: You must be logged in to purchase a membership.");
}

// Check if gym_id is set
if (!isset($_GET['gym_id'])) {
    die("Error: No gym selected.");
}
$gym_id = $_GET['gym_id'];

// Fetch gym details
$gym = fetchGymDetails($conn, $gym_id);

if (!$gym) {
    die("Error: Gym not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $duration = intval($_POST['membership_duration']);

    if (empty($user_id)) {
        die("Error: user_id is missing from session.");
    }

    if (hasActiveMembership($conn, $user_id, $gym_id)) {
        echo "<script>alert('You already have an active membership for this gym!'); window.location.href='gyms.php';</script>";
        exit();
    }

    if (purchaseMembership($conn, $user_id, $gym_id, $name, $duration)) {
        echo "<script>alert('Membership purchased successfully!'); window.location.href='gyms.php';</script>";
    } else {
        die("Error: Failed to purchase membership.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Membership</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
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
