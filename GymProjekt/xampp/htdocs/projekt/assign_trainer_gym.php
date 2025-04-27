<?php
session_start();
header("Content-Type: application/json");
include "db.php";

// Check if user is logged in and is an owner
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate required POST data
    if (empty($_POST["trainer_id"]) || empty($_POST["gym_id"])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing trainer or gym ID"]);
        exit();
    }

    $trainer_id = trim($_POST["trainer_id"]);
    $gym_id = trim($_POST["gym_id"]);
    $owner_id = $_SESSION['user_id'];

    // Make sure the gym belongs to the owner
    $check = $conn->prepare("SELECT 1 FROM gym WHERE gym_id = ? AND owner_id = ?");
    $check->bind_param("si", $gym_id, $owner_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        http_response_code(403);
        echo json_encode(["error" => "You do not own this gym"]);
        exit();
    }

    // Insert relationship between trainer and gym (ignores duplicates)
    $stmt = $conn->prepare("INSERT IGNORE INTO trainer_gym (trainer_id, gym_id) VALUES (?, ?)");
    $stmt->bind_param("ss", $trainer_id, $gym_id);
    if ($stmt->execute()) {
        echo json_encode(["status" => "assigned"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Database error"]);
    }
}