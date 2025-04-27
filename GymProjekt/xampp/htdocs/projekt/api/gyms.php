<?php
session_start();
include "../db.php";

header("Content-Type: application/json");

// Make sure user is logged in and is an owner
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$owner_id = $_SESSION['user_id'];

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Fetch all gyms owned by the logged-in user
        $stmt = $conn->prepare("SELECT * FROM gym WHERE owner_id = ?");
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $gyms = [];

        while ($row = $result->fetch_assoc()) {
            $gyms[] = $row;
        }

        echo json_encode($gyms);
        break;

    case 'POST':
        // Add a new gym (expecting JSON)
        $input = json_decode(file_get_contents("php://input"), true);

        $gym_name = trim($input['gym_name'] ?? '');
        $gym_address = trim($input['gym_address'] ?? '');
        $gym_type = trim($input['gym_type'] ?? '');

        if (!$gym_name || !$gym_address || !$gym_type) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit;
        }

        $gym_id = uniqid("G");
        $stmt = $conn->prepare("INSERT INTO gym (gym_id, gym_name, gym_address, gym_type, owner_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $gym_id, $gym_name, $gym_address, $gym_type, $owner_id);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "gym_id" => $gym_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to add gym"]);
        }
        break;

    case 'PUT':
        // Edit a gym (expecting form-urlencoded)
        parse_str(file_get_contents("php://input"), $input);

        $gym_id = $input['gym_id'] ?? '';
        $gym_name = trim($input['gym_name'] ?? '');
        $gym_address = trim($input['gym_address'] ?? '');
        $gym_type = trim($input['gym_type'] ?? '');

        if (!$gym_id || !$gym_name || !$gym_address || !$gym_type) {
            http_response_code(400);
            echo json_encode(["error" => "Missing fields"]);
            exit;
        }

        // Ensure the gym belongs to the current owner
        $check = $conn->prepare("SELECT * FROM gym WHERE gym_id = ? AND owner_id = ?");
        $check->bind_param("si", $gym_id, $owner_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            http_response_code(403);
            echo json_encode(["error" => "Access denied"]);
            exit;
        }

        $stmt = $conn->prepare("UPDATE gym SET gym_name = ?, gym_address = ?, gym_type = ? WHERE gym_id = ?");
        $stmt->bind_param("ssss", $gym_name, $gym_address, $gym_type, $gym_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "updated"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update"]);
        }
        break;

    case 'DELETE':
        // Delete a gym (expecting form-urlencoded)
        parse_str(file_get_contents("php://input"), $input);
        $gym_id = $input['gym_id'] ?? '';

        if (!$gym_id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing gym_id"]);
            exit;
        }

        // Ensure the gym belongs to the current owner
        $check = $conn->prepare("SELECT * FROM gym WHERE gym_id = ? AND owner_id = ?");
        $check->bind_param("si", $gym_id, $owner_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows === 0) {
            http_response_code(403);
            echo json_encode(["error" => "Access denied"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM gym WHERE gym_id = ?");
        $stmt->bind_param("s", $gym_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "deleted"]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Delete failed"]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
