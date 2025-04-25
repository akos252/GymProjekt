<?php
session_start();
header("Content-Type: application/json");
include "../db.php";

// Ensure only owners can access this API
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$owner_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    //GET: Retrieve all trainers associated with this owner's gyms
    case "GET":
        $sql = "SELECT DISTINCT t.trainer_id, t.name, t.mobilenum
                FROM trainer t
                JOIN trainer_gym tg ON t.trainer_id = tg.trainer_id
                JOIN gym g ON tg.gym_id = g.gym_id
                WHERE g.owner_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $owner_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $trainers = [];
        while ($row = $result->fetch_assoc()) {
            $trainers[] = $row;
        }

        echo json_encode($trainers);
        break;

    //POST: Create a new trainer
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        $trainer_id = uniqid("T");
        $name = $data["name"];
        $mobilenum = $data["mobilenum"];
        $image = ""; // Image upload not handled via this API yet

        $sql = "INSERT INTO trainer (trainer_id, name, time, mobilenum, image) VALUES (?, ?, '', ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $trainer_id, $name, $mobilenum, $image);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "trainer_id" => $trainer_id]);
        } else {
            echo json_encode(["error" => "Failed to create trainer"]);
        }
        break;

    //PUT: Update a trainer's mobile number
    case "PUT":
        parse_str(file_get_contents("php://input"), $_PUT);
        $trainer_id = $_PUT["trainer_id"];
        $mobilenum = $_PUT["mobilenum"];

        $sql = "UPDATE trainer SET mobilenum = ? WHERE trainer_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $mobilenum, $trainer_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "updated"]);
        } else {
            echo json_encode(["error" => "Failed to update trainer"]);
        }
        break;

    //DELETE: Remove trainer from all gyms and delete the trainer
    case "DELETE":
        parse_str(file_get_contents("php://input"), $_DELETE);
        $trainer_id = $_DELETE["trainer_id"];

        // Check if this trainer belongs to one of the owner's gyms
        $check = $conn->prepare("SELECT tg.trainer_id FROM trainer_gym tg 
                                JOIN gym g ON tg.gym_id = g.gym_id 
                                WHERE tg.trainer_id = ? AND g.owner_id = ?");
        $check->bind_param("si", $trainer_id, $owner_id);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows === 0) {
            http_response_code(403);
            echo json_encode(["error" => "Not your trainer"]);
            exit();
        }

        // Delete trainer-gym mappings and the trainer itself
        $conn->query("DELETE FROM trainer_gym WHERE trainer_id = '$trainer_id'");
        $conn->query("DELETE FROM trainer WHERE trainer_id = '$trainer_id'");
        echo json_encode(["status" => "deleted"]);
        break;

    //If method is not supported
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method Not Allowed"]);
}
