<?php
session_start();
header("Content-Type: application/json");
include "../db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$owner_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // GET ALL TRAINERS FOR THIS OWNER
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

    // CREATE A TRAINER
    case "POST":
        $data = json_decode(file_get_contents("php://input"), true);
        $trainer_id = uniqid("T");
        $name = $data["name"];
        $mobilenum = $data["mobilenum"];
        $image = ""; // Optional for now

        $sql = "INSERT INTO trainer (trainer_id, name, time, mobilenum, image) VALUES (?, ?, '', ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $trainer_id, $name, $mobilenum, $image);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "trainer_id" => $trainer_id]);
        } else {
            echo json_encode(["error" => "Failed to create trainer"]);
        }
        break;

    // UPDATE PHONE NUMBER
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

    // DELETE TRAINER COMPLETELY
    case "DELETE":
        parse_str(file_get_contents("php://input"), $_DELETE);
        $trainer_id = $_DELETE["trainer_id"];

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

        $conn->query("DELETE FROM trainer_gym WHERE trainer_id = '$trainer_id'");
        $conn->query("DELETE FROM trainer WHERE trainer_id = '$trainer_id'");
        echo json_encode(["status" => "deleted"]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method Not Allowed"]);
}
