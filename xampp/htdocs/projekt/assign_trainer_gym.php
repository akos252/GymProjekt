<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    http_response_code(403);
    echo "Unauthorized";
    exit;
}

$trainer_id = $_POST['trainer_id'] ?? null;
$gym_id = $_POST['gym_id'] ?? null;
$action = $_POST['action'] ?? null;

$owner_id = $_SESSION['user_id'];

// Confirm the gym is owned by this owner
$check = $conn->prepare("SELECT gym_id FROM gym WHERE gym_id = ? AND owner_id = ?");
$check->bind_param("si", $gym_id, $owner_id);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows === 0) {
    http_response_code(403);
    echo "You don't own this gym";
    exit;
}

if ($action === "assign") {
    $insert = $conn->prepare("INSERT IGNORE INTO trainer_gym (trainer_id, gym_id) VALUES (?, ?)");
    $insert->bind_param("ss", $trainer_id, $gym_id);
    $insert->execute();
    echo "assigned";
} elseif ($action === "unassign") {
    $delete = $conn->prepare("DELETE FROM trainer_gym WHERE trainer_id = ? AND gym_id = ?");
    $delete->bind_param("ss", $trainer_id, $gym_id);
    $delete->execute();
    echo "unassigned";
} else {
    http_response_code(400);
    echo "Invalid action";
}
?>
