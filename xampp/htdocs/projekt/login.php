<?php
session_start();
include "db.php";
include "navbar.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $sql = "SELECT id, username, user_type FROM login WHERE username = ? AND pwd = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // Fetch user data

    if ($user) {
        //Store username, user_id and user_type in session
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
    <meta charset="UTF-8">
    <title>Gym Login</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="container">
    <h2>Login to Gym Management System</h2>
    <form id="login-form">
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