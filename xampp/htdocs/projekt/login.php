<?php
session_start();
include "db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Ensure the database table name and column names match your schema
    $sql = "SELECT id, username, user_type FROM login WHERE username = ? AND pwd = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // Fetch user data

    if ($user) {
        // ✅ Store both username & user_id in session
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = $user['user_type'];

        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gym Login</title>
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
    <div class = container>
    <h2>Login to Gym Management</h2>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button class="btn" style="margin-top: 5px;" type="submit">Login</button>
    </form>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <br>Don't have an account yet? Register <a style="color: red;" href="register.php">here</a>
    </div>
</body>
</html>