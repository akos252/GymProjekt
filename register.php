<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // egyező jelszó ellenőrése
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // létező felhasználónév ellenőrzése
        $check_user = "SELECT * FROM login WHERE username = ?";
        $stmt = $conn->prepare($check_user);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username already exists. Choose another!";
        } else {
            // új felhasználó beszúrása
            $insert_user = "INSERT INTO login (username, pwd) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_user);
            $stmt->bind_param("ss", $username, $password);
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
    <h2>Register</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
        <button type="submit">Register</button>
    </form>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <p>Already have an account? <a href="login.php">Login here</a></p>
</body>
</html>