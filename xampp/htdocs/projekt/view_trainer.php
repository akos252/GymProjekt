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

// Fetch gyms this trainer is assigned to
$gym_q = $conn->prepare("
    SELECT g.gym_id, g.gym_name
    FROM gym g
    JOIN trainer_gym tg ON tg.gym_id = g.gym_id
    WHERE tg.trainer_id = ?
");
$gym_q->bind_param("s", $trainer_id);
$gym_q->execute();
$gyms = $gym_q->get_result();
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

    <div class="container"
        style="max-width: 600px; margin: auto; background: #1e1e1e; padding: 20px; border-radius: 10px;">
        <h2 style="color: red;"><?php echo htmlspecialchars($trainer['name']); ?></h2>

        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>

        <p><strong>📞 Mobile:</strong> <span id="mobile-display"><?php echo htmlspecialchars($trainer['mobilenum']); ?></span></p>


        <?php if (!empty($trainer['image'])): ?>
        <img src="data:image/jpeg;base64,<?php echo base64_encode($trainer['image']); ?>" alt="Trainer Image"
            style="max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 15px;">
        <?php endif; ?>

        <h4>🏋️ Assigned Gyms:</h4>
        <ul style="color: #ccc;">
            <?php while ($g = $gyms->fetch_assoc()): ?>
            <li>
                <a href="view_gym.php?gym_id=<?php echo htmlspecialchars($g['gym_id']); ?>"
                    style="color: red; text-decoration: underline;">
                    <?php echo htmlspecialchars($g['gym_name']); ?>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>

        <h4>Edit Mobile Number</h4>
        <form id="update-trainer-form">
    <input type="text" name="mobilenum" id="mobilenum" value="<?php echo htmlspecialchars($trainer['mobilenum']); ?>" required>
    <button class="btn" type="submit">✏️ Update</button>
    <div id="update-msg" style="margin-top: 8px;"></div>
</form>

<button class="btn" id="delete-btn" style="background: #a00; color: white; margin-top: 15px;">🗑️ Delete Trainer</button>

    </div>
    <script>
document.getElementById("update-trainer-form").addEventListener("submit", function (e) {
    e.preventDefault();

    const mobile = document.getElementById("mobilenum").value;
    const trainerId = "<?php echo $trainer_id; ?>";

    fetch("update_trainer.php", {
        method: "POST",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: "trainer_id=" + encodeURIComponent(trainerId) + "&mobilenum=" + encodeURIComponent(mobile)
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("mobile-display").textContent = mobile;
        document.getElementById("update-msg").innerHTML = `<p style='color: green;'>✅ ${data}</p>`;
    })
    .catch(() => {
        document.getElementById("update-msg").innerHTML = `<p style='color: red;'>❌ Update failed.</p>`;
    });
});

document.getElementById("delete-btn").addEventListener("click", function () {
    if (!confirm("Are you sure you want to delete this trainer from all gyms?")) return;

    fetch("delete_trainer.php", {
        method: "POST",
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: "trainer_id=" + encodeURIComponent("<?php echo $trainer_id; ?>")
    })
    .then(res => res.text())
    .then(data => {
        alert("✅ Trainer deleted.");
        window.location.href = "owner_dashboard.php";
    })
    .catch(() => {
        alert("❌ Failed to delete trainer.");
    });
});
</script>

</body>

</html>