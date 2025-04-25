<?php
session_start();
include "db.php";
include "navbar.php";
require_once "includes/owner_functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $mobilenum = $_POST['mobilenum'];
    $dob = $_POST['dob'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    $result = registerOwner($conn, $username, $password, $confirm_password, $fullname, $email, $mobilenum, $dob);

    if ($result === true) {
        header("Location: login.php?registered=owner");
        exit();
    } else {
        $error = $result;
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Register as Gym Owner - Gym Management</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">
    <h2>Register as Gym Owner</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <input type="text" name="mobilenum" placeholder="Mobile Number" required><br>
        <input type="date" name="dob" placeholder="Date of Birth" required><br>
        <input type="text" name="fullname" placeholder="Full Name" required><br>
        <input type="email" name="email" placeholder="Email Address"><br>
        <button class="btn" style="margin-top: 5px;" type="submit">Register as Owner</button>
    </form>

    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    
    <p>Already have an account? <a style="color: red;" href="login.php">Login here</a></p>
    <p>Just want to join a gym? <a style="color: red;" href="register.php">Register as a Member</a></p>
</div>
</body>
</html>