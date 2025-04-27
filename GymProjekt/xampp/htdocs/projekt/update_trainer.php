<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    exit("Unauthorized.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['trainer_id'], $_POST['mobilenum'])) {
    $trainer_id = $_POST['trainer_id'];
    $mobilenum = trim($_POST['mobilenum']);
    $owner_id = $_SESSION['user_id'];

    // Validate ownership
    $check = $conn->prepare("
        SELECT 1 FROM trainer_gym tg
        JOIN gym g ON g.gym_id = tg.gym_id
        WHERE tg.trainer_id = ? AND g.owner_id = ?
    ");
    $check->bind_param("si", $trainer_id, $owner_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        exit("Unauthorized.");
    }

    $update = $conn->prepare("UPDATE trainer SET mobilenum = ? WHERE trainer_id = ?");
    $update->bind_param("ss", $mobilenum, $trainer_id);

    if ($update->execute()) {
        echo "Mobile number updated!";
    } else {
        echo "Update failed.";
    }
}
?>
