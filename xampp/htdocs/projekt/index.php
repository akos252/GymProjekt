<?php
session_start();

// Check if the request is an API request (JSON) or a regular browser request
$is_api_request = isset($_GET['api']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

$response = [];

if (isset($_SESSION['username'])) {
    $response = [
        "status" => "success",
        "message" => "Welcome to the Gym Management System",
        "username" => $_SESSION['username'],
        "dashboard_url" => "dashboard.php",
        "logout_url" => "logout.php"
    ];
} else {
    $response = [
        "status" => "unauthorized",
        "message" => "You are not logged in.",
        "login_url" => "login.php"
    ];
}

// Return JSON if API request
if ($is_api_request) {
    header("Content-Type: application/json");
    echo json_encode($response);
    exit();
}

// Otherwise, render HTML
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Gym Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Gym Management System</h1>
        <p>Manage your gym members, trainers, and more with ease.</p>
        
        <?php if (isset($_SESSION['username'])): ?>
            <p>You are logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>.</p>
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
            <a href="logout.php" class="btn">Logout</a>
        <?php else: ?>
            <a href="login.php" class="btn">Login</a>
        <?php endif; ?>
    </div>

    <!-- Optional: JavaScript for Fetching API Data -->
    <script>
        fetch('index.php?api=true')
            .then(response => response.json())
            .then(data => console.log("API Response:", data))
            .catch(error => console.error("Error fetching API:", error));
    </script>
</body>
</html>
