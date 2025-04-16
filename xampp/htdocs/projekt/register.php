<?php 
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $mobilenum = trim($_POST['mobilenum']);
    $dob = trim($_POST['dob']);

    // Password match check
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if username already exists
        $check_user = "SELECT * FROM login WHERE username = ? OR mobilenum = ?";
        $stmt = $conn->prepare($check_user);
        $stmt->bind_param("ss", $username, $mobilenum);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username or mobile number already exists. Choose another!";
        } else {
            // Insert new user with username, password, mobile number, and date of birth
            $insert_user = "INSERT INTO login (username, pwd, mobilenum, dob) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_user);
            $stmt->bind_param("ssss", $username, $password, $mobilenum, $dob);
            if ($stmt->execute()) {
                header("Location: login.php?registered=success");
                exit();
            } else {
                $error = "Registration failed. Try again!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Gym Management</title>
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
        <h2>Register</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <input type="text" name="mobilenum" placeholder="Mobile Number" required><br>
            <input type="date" name="dob" placeholder="Date of Birth" required><br>
            <button class="btn" style="margin-top: 5px;" type="submit">Register</button>
        </form>
        <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
        <p>Already have an account? Login <a style="color: red;" href="login.php"> here</a></p>
        <p>Want to manage a gym? <a style="color: red;" href="owner_register.php">Register as a Gym Owner</a></p>
    </div>
</body>
</html>