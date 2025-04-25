<?php
session_start();
header("Content-Type: application/json");
include "../db.php"; // Connect to the database

// Decode incoming JSON request body
$data = json_decode(file_get_contents("php://input"), true);

// Validate input: make sure username and password are provided
if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400); // Bad request
    echo json_encode(["error" => "Missing username or password"]);
    exit;
}

$username = trim($data['username']);
$password = trim($data['password']);

// Prepare and execute query to check credentials
$stmt = $conn->prepare("SELECT id, username, user_type FROM login WHERE username = ? AND pwd = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

// If user is found, log them in
if ($user = $result->fetch_assoc()) {
    // Store user data in session
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['user_type'];

    // Respond with success message and user info
    echo json_encode([
        "status" => "success",
        "message" => "Logged in successfully",
        "username" => $user['username'],
        "user_type" => $user['user_type']
    ]);
} else {
    // Invalid credentials
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "Invalid credentials"]);
}
