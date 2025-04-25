<?php
session_start();
header("Content-Type: application/json");
include "db.php";

// Only owners are authorized
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST['trainer_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Trainer ID is missing"]);
        exit();
    }

    $trainer_id = $_POST['trainer_id'];
    $owner_id = $_SESSION['user_id'];

    // Check if the trainer exists and belongs to the owner, even if not assigned to a gym
    $check = $conn->prepare("
        SELECT 1
        FROM trainer t
        LEFT JOIN trainer_gym tg ON t.trainer_id = tg.trainer_id
        LEFT JOIN gym g ON tg.gym_id = g.gym_id
        WHERE t.trainer_id = ? AND (g.owner_id = ? OR g.owner_id IS NULL)
    ");
    $check->bind_param("si", $trainer_id, $owner_id);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows === 0) {
        http_response_code(403);
        echo json_encode(["error" => "You are not authorized to delete this trainer"]);
        exit();
    }

    // Delete from trainer_gym (if exists)
    $stmt1 = $conn->prepare("DELETE FROM trainer_gym WHERE trainer_id = ?");
    $stmt1->bind_param("s", $trainer_id);
    $stmt1->execute();

    // Delete from trainer table
    $stmt2 = $conn->prepare("DELETE FROM trainer WHERE trainer_id = ?");
    $stmt2->bind_param("s", $trainer_id);
    $stmt2->execute();

    echo json_encode(["status" => "deleted"]);
    exit();
}

// Fallback: reject other request methods
http_response_code(405);
echo json_encode(["error" => "Method not allowed"]);
exit();
