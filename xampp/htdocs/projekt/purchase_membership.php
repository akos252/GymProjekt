<?php
session_start();
include "db.php";
include "navbar.php";

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
$stmt = $conn->prepare("SELECT gym_name FROM gym WHERE gym_id = ?");
$stmt->bind_param("s", $gym_id);
$stmt->execute();
$result = $stmt->get_result();
$gym = $result->fetch_assoc();

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

    //Check for ACTIVE membership
    $check = $conn->prepare("
        SELECT membership_id FROM memberships 
        WHERE user_id = ? AND gym_id = ? AND end_date >= CURDATE()
    ");
    $check->bind_param("is", $user_id, $gym_id);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('You already have an active membership for this gym!'); window.location.href='gyms.php';</script>";
        exit();
    }

    //Calculate membership dates
    $start_date = date("Y-m-d");
    $end_date = date("Y-m-d", strtotime("+$duration days"));

    //Insert membership
    $stmt = $conn->prepare("INSERT INTO memberships (user_id, gym_id, name, start_date, end_date) VALUES (?, ?, ?, ?, ?)");

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