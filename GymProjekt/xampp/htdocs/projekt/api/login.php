<?php
session_start();
header("Content-Type: application/json");
include "../db.php";
require_once "../includes/login_functions.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing username or password"]);
    exit;
}

$username = $data['username'];
$password = $data['password'];

$result = loginUser($conn, $username, $password);

if (is_array($result)) {
    $_SESSION['username'] = $result['username'];
    $_SESSION['user_id'] = $result['id'];
    $_SESSION['user_type'] = $result['user_type'];

    echo json_encode([
        "status" => "success",
        "message" => "Logged in successfully",
        "username" => $result['username'],
        "user_type" => $result['user_type']
    ]);
} else {
    http_response_code(401);
    echo json_encode(["error" => $result]);
}
