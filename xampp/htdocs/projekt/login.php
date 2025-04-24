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
    <title>Gym Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<!-- Navigation Bar -->
<nav class="navbar">
    <div class="nav-left">
        <a href="index.php">
            <img src="asstets/logo.png" alt="GMS Logo" class="logo">
        </a>
    </div>
    <div class="nav-center">
        <a href="gyms.php">View Gyms</a>

        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'owner'): ?>
            <a href="owner_dashboard.php" style="color: red;">Owner Dashboard</a>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <a href="<?php echo isset($_SESSION['username']) ? 'profile.php' : 'login.php'; ?>" class="btn">
            <?php echo isset($_SESSION['username']) ? 'Profile' : 'Login'; ?>
        </a>
    </div>
</nav>
</nav>
    <div class = container>
    <h2>Login to Gym Management System</h2>
    <form id="login-form">
    <input type="text" name="username" placeholder="Username" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button class="btn" type="submit">Login</button>
    <p id="login-error" style="color: red;"></p>
</form>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <br>Don't have an account yet? Register <a style="color: red;" href="register.php">here</a>
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