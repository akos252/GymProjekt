<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['trainer_id'])) {
    $trainer_id = $_POST['trainer_id'];
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
        die("Unauthorized.");
    }

    // Delete from trainer_gym
    $del1 = $conn->prepare("DELETE FROM trainer_gym WHERE trainer_id = ?");
    $del1->bind_param("s", $trainer_id);
    $del1->execute();

    // Delete from trainer
    $del2 = $conn->prepare("DELETE FROM trainer WHERE trainer_id = ?");
    $del2->bind_param("s", $trainer_id);

    if ($del2->execute()) {
        echo "
            <script>
                alert('✅ Trainer successfully deleted from all gyms.');
                window.location.href = 'owner_dashboard.php';
            </script>
        ";
    } else {
        echo "
            <script>
                alert('❌ Failed to delete trainer.');
                window.location.href = 'owner_dashboard.php';
            </script>
        ";
    }
    exit();
} else {
    header("Location: owner_dashboard.php");
    exit();
}
?>
