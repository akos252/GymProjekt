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

// Check ownership
$check = $conn->prepare("
    SELECT t.name, t.mobilenum, t.image
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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_trainer_btn'])) {
    $new_mobile = trim($_POST['mobilenum']);
    $update = $conn->prepare("UPDATE trainer SET mobilenum = ? WHERE trainer_id = ?");
    $update->bind_param("ss", $new_mobile, $trainer_id);
    $update->execute();
    $trainer['mobilenum'] = $new_mobile;
    $success = "Mobile number updated ✅";
}

// Fetch assigned gyms
$assigned_gym_ids = [];
$assigned = $conn->prepare("
    SELECT gym_id FROM trainer_gym WHERE trainer_id = ?
");
$assigned->bind_param("s", $trainer_id);
$assigned->execute();
$assigned_result = $assigned->get_result();
while ($row = $assigned_result->fetch_assoc()) {
    $assigned_gym_ids[] = $row['gym_id'];
}

// Fetch all gyms owned by the owner
$gyms_all = $conn->prepare("
    SELECT gym_id, gym_name FROM gym WHERE owner_id = ?
");
$gyms_all->bind_param("i", $owner_id);
$gyms_all->execute();
$gyms_list = $gyms_all->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Trainer Details - GMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-left">
        <a href="index.php"><img src="asstets/logo.png" alt="GMS Logo" class="logo"></a>
    </div>
    <div class="nav-center">
        <a href="owner_dashboard.php">Dashboard</a>
    </div>
    <div class="nav-right">
        <a href="logout.php" class="btn">Logout</a>
    </div>
</nav>

<div class="container" style="max-width: 600px; margin: auto; background: #1e1e1e; padding: 20px; border-radius: 10px;">
    <h2 style="color: red;"><?php echo htmlspecialchars($trainer['name']); ?></h2>

    <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>

    <p><strong>📞 Mobile:</strong> <?php echo htmlspecialchars($trainer['mobilenum']); ?></p>

    <?php if (!empty($trainer['image'])): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($trainer['image']); ?>" 
             alt="Trainer Image" 
             style="max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 15px;">
    <?php endif; ?>

    <h4>Assign to Gyms:</h4>
<div class="tcheckbox-grid">
    <?php while ($gym = $gyms_list->fetch_assoc()): ?>
        <label>
            <input type="checkbox"
                   name="gyms[]"
                   value="<?php echo $gym['gym_id']; ?>"
                   <?php echo in_array($gym['gym_id'], $assigned_gym_ids) ? 'checked' : ''; ?>>
            <?php echo htmlspecialchars($gym['gym_name']); ?>
        </label>
    <?php endwhile; ?>
</div>

    <h4>Edit Mobile Number</h4>
    <form method="POST">
        <input type="text" name="mobilenum" value="<?php echo htmlspecialchars($trainer['mobilenum']); ?>" required>
        <button class="btn" type="submit" name="update_trainer_btn">✏️ Update</button>
    </form>

    <form action="delete_trainer.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this trainer from all gyms?')">
        <input type="hidden" name="trainer_id" value="<?php echo $trainer_id; ?>">
        <button class="btn" style="background: #a00; color: white; margin-top: 15px;">🗑️ Delete Trainer</button>
    </form>
</div>

<script>
document.querySelectorAll('#gym-assign-form input[type="checkbox"]').forEach(box => {
    box.addEventListener("change", () => {
        const trainerId = "<?php echo $trainer_id; ?>";
        const gymId = box.value;
        const action = box.checked ? "assign" : "unassign";

        fetch("assign_trainer_gym.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `trainer_id=${encodeURIComponent(trainerId)}&gym_id=${encodeURIComponent(gymId)}&action=${action}`
        })
        .then(res => res.text())
        .then(data => {
            console.log("✅ " + data);
        })
        .catch(() => {
            alert("❌ Failed to update assignment.");
            box.checked = !box.checked; // rollback
        });
    });
});
</script>

</body>
</html>
