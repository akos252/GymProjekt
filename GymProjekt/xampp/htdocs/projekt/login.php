<?php
session_start();
include "db.php";
include "navbar.php";
require_once "includes/login_functions.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = loginUser($conn, $username, $password);

    if (is_array($result)) {
        $_SESSION['username'] = $result['username'];
        $_SESSION['user_id'] = $result['id'];
        $_SESSION['user_type'] = $result['user_type'];

        header("Location: index.php");
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
    <title>Gym Login</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="container">
    <h2>Login to Gym Management System</h2>
    <form id="login-form" method="post">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button class="btn" type="submit">Login</button>
        <p id="login-error" style="color: red;"></p>
    </form>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <p style="margin-top: 15px;">
        Don't have an account yet? 
        <a style="color: red;" href="register.php">Register here</a>
    </p>
</div>

<script>
document.getElementById("login-form").addEventListener("submit", function(e) {
    e.preventDefault();
    const form = e.target;
    
    fetch("api/login.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            username: form.username.value,
            password: form.password.value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === "success") {
            window.location.href = "index.php";
        } else {
            document.getElementById("login-error").textContent = data.error || "Login failed";
        }
    })
    .catch(() => {
        document.getElementById("login-error").textContent = "Network error";
    });
});
</script>

</body>
</html>
