<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    die("Access denied.");
}

$owner_id = $_SESSION['user_id'];

if (!isset($_GET['trainer_id'])) {
    die("No trainer selected.");
}

$trainer_id = $_GET['trainer_id'];

// Ownership check
$check = $conn->prepare("
    SELECT t.name, t.time, t.mobilenum 
    FROM trainer t
    JOIN trainer_gym tg ON tg.trainer_id = t.trainer_id
    JOIN gym g ON g.gym_id = tg.gym_id
    WHERE t.trainer_id = ? AND g.owner_id = ?
    LIMIT 1
");
$check->bind_param("si", $trainer_id, $owner_id);
$check->execute();
$result = $check->get_result();
if ($result->num_rows === 0) {
    die("Unauthorized or trainer not found.");
}
$trainer = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_time = trim($_POST['trainer_time']);
    $new_mobile = trim($_POST['trainer_mobilenum']);

    $update = $conn->prepare("UPDATE trainer SET time = ?, mobilenum = ? WHERE trainer_id = ?");
    $update->bind_param("sss", $new_time, $new_mobile, $trainer_id);
    if ($update->execute()) {
        $success = "Trainer updated successfully ✅";
        $trainer['time'] = $new_time;
        $trainer['mobilenum'] = $new_mobile;
    } else {
        $error = "Failed to update trainer.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Trainer - GMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-left">
        <a href="index.php"><img src="asstets/logo.png" alt="GMS Logo" class="logo"></a>
    </div>
    <div class="nav-center">
        <a href="owner_dashboard.php">Dashboard</a>
        <a href="gyms.php">View gyms</a>
    </div>
    <div class="nav-right">
        <a href="logout.php" class="btn">Logout</a>
    </div>
</nav>

<div class="container" style="max-width: 500px; margin: auto;">
    <h2>Edit Trainer: <?php echo htmlspecialchars($trainer['name']); ?></h2>

    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>

    <form method="post">
        <input type="text" name="trainer_time" value="<?php echo htmlspecialchars($trainer['time']); ?>" placeholder="Availability Time" required><br>
        <input type="text" name="trainer_mobilenum" value="<?php echo htmlspecialchars($trainer['mobilenum']); ?>" placeholder="Mobile Number" required><br>
        <button class="btn" type="submit">Save Changes</button>
    </form>
</div>

</body>
</html>
