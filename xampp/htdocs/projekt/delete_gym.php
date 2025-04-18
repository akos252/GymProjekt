<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    exit("unauthorized");
}

$owner_id = $_SESSION['user_id'];

if (!isset($_POST['gym_id'])) {
    exit("no_gym_id");
}

$gym_id = $_POST['gym_id'];

// Make sure the gym belongs to the current owner
$check = $conn->prepare("SELECT * FROM gym WHERE gym_id = ? AND owner_id = ?");
$check->bind_param("si", $gym_id, $owner_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    exit("not_yours");
}

// Delete the gym
$delete = $conn->prepare("DELETE FROM gym WHERE gym_id = ? AND owner_id = ?");
$delete->bind_param("si", $gym_id, $owner_id);

if ($delete->execute()) {
    echo "deleted";
} else {
    echo "fail";
}
?>