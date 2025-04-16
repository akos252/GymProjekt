<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $mobilenum = trim($_POST['mobilenum']);
    $dob = trim($_POST['dob']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $check_user = "SELECT * FROM login WHERE username = ? OR mobilenum = ?";
        $stmt = $conn->prepare($check_user);
        $stmt->bind_param("ss", $username, $mobilenum);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or mobile number already exists.";
        } else {
            // Insert into login table with user_type 'owner'
            $insert_login = "INSERT INTO login (username, pwd, mobilenum, dob, user_type) VALUES (?, ?, ?, ?, 'owner')";
            $stmt = $conn->prepare($insert_login);
            $stmt->bind_param("ssss", $username, $password, $mobilenum, $dob);

            if ($stmt->execute()) {
                $owner_id = $stmt->insert_id;

                // Insert into owner table
                $insert_owner = "INSERT INTO owner (owner_id, full_name, email, contact_number) VALUES (?, ?, ?, ?)";
                $stmt_owner = $conn->prepare($insert_owner);
                $stmt_owner->bind_param("isss", $owner_id, $fullname, $email, $mobilenum);
                $stmt_owner->execute();

                header("Location: login.php?registered=owner");
                exit();
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register as Gym Owner - Gym Management</title>
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
        <a href="profile.php" class="btn">Profile</a>
    </div>
</nav>

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