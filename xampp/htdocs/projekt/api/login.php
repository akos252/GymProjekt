<?php
session_start();
header("Content-Type: application/json");
include "../db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing username or password"]);
    exit;
}

$username = trim($data['username']);
$password = trim($data['password']);

$stmt = $conn->prepare("SELECT id, username, user_type FROM login WHERE username = ? AND pwd = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['user_type'];

    echo json_encode([
        "status" => "success",
        "message" => "Logged in successfully",
        "username" => $user['username'],
        "user_type" => $user['user_type']
    ]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid credentials"]);
}
