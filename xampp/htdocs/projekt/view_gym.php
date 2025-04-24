<?php
session_start();
include "db.php";

if (!isset($_GET['gym_id'])) {
    die("Error: No gym selected.");
}

$gym_id = $_GET['gym_id'];

// Fetch gym details
$sql = "SELECT gym_name FROM gym WHERE gym_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $gym_id);
$stmt->execute();
$result = $stmt->get_result();
$gym = $result->fetch_assoc();

if (!$gym) {
    die("Error: Gym not found.");
}
$is_owner_of_gym = false;

if (isset($_SESSION['user_id'], $_SESSION['user_type']) && $_SESSION['user_type'] === 'owner') {
    $owner_check = $conn->prepare("SELECT 1 FROM gym WHERE gym_id = ? AND owner_id = ?");
    $owner_check->bind_param("si", $gym_id, $_SESSION['user_id']);
    $owner_check->execute();
    $owner_check_result = $owner_check->get_result();

    if ($owner_check_result->num_rows > 0) {
        $is_owner_of_gym = true;
    }
}

// Fetch trainers at this gym, including images and IDs
$trainer_sql = "SELECT t.trainer_id, t.name, t.time, t.mobilenum, t.image FROM trainer t 
                INNER JOIN trainer_gym tg ON t.trainer_id = tg.trainer_id
                WHERE tg.gym_id = ?";
$trainer_stmt = $conn->prepare($trainer_sql);
$trainer_stmt->bind_param("s", $gym_id);
$trainer_stmt->execute();
$trainer_result = $trainer_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($gym['gym_name']); ?> - Gym Details</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">
    <div class="nav-left">
        <a href="index.php">
            <img src="asstets/logo.png" alt="GMS Logo" class="logo">
        </a>
    </div>
    <div class="nav-center">
        <a href="gyms.php">View Gyms</a>

        <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'owner'): ?>
            <a href="owner_dashboard.php" style="color: red;">Owner Dashboard</a>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <a href="<?php echo isset($_SESSION['username']) ? 'profile.php' : 'login.php'; ?>" class="btn">
            <?php echo isset($_SESSION['username']) ? 'Profile' : 'Login'; ?>
        </a>
    </div>
</nav>

    <div style="max-width: 600px; margin: auto; padding: 20px; background: #1e1e1e; border-radius: 10px;">
        <h2 style="color: red;"><?php echo htmlspecialchars($gym['gym_name']); ?></h2>

        <h3>Available Personal Trainers</h3>
        <?php if ($trainer_result->num_rows > 0): ?>
        <ul style="list-style: none; padding: 0;">
            <?php while ($trainer = $trainer_result->fetch_assoc()): ?>
            <li style="background: #333; padding: 10px; margin: 5px; border-radius: 5px; text-align: center;">
                <strong><?php echo htmlspecialchars($trainer['name']); ?></strong><br>
                Time: <?php echo htmlspecialchars($trainer['time']); ?><br>
                Contact: <?php echo htmlspecialchars($trainer['mobilenum']); ?><br>

                <?php if (!empty($trainer['image'])): ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($trainer['image']); ?>" alt="Trainer Image"
                    style="max-width: 270px; height: auto; display: block; margin: auto; border-radius: 10px;">
                <?php else: ?>
                <p style="color: gray;">No image available.</p>
                <?php endif; ?>

                <?php if ($is_owner_of_gym): ?>
                <a href="edit_trainer.php?trainer_id=<?php echo $trainer['trainer_id']; ?>"
                    style="display:inline-block; margin-top: 10px; padding: 6px 12px; background: #444; color: white; border-radius: 5px; text-decoration: none;">
                    ✏️ Edit
                </a>

                <button class="btn delete-trainer-btn" data-trainer-id="<?php echo $trainer['trainer_id']; ?>"
                    style="margin-top: 10px; background: #a00; color: white; padding: 6px 12px; border: none; border-radius: 5px; cursor: pointer;">
                    🗑️ Delete
                </button>
                <?php endif; ?>

            </li>
            <?php endwhile; ?>
        </ul>
        <?php else: ?>
        <p style="color: gray;">No trainers available.</p>
        <?php endif; ?>

        <a href="purchase_membership.php?gym_id=<?php echo $gym_id; ?>"
            style="display: inline-block; margin-top: 15px; background: red; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
            Purchase Membership
        </a>
    </div>

    <script>
        document.addEventListener("click", function (e) {
            if (e.target.classList.contains("delete-trainer-btn")) {
                const trainerId = e.target.dataset.trainerId;

                if (!confirm("Are you sure you want to delete this trainer?")) return;

                fetch("delete_trainer.php", {
                        method: "POST",
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: "trainer_id=" + encodeURIComponent(trainerId)
                    })
                    .then(res => res.text())
                    .then(data => {
                        if (data.trim() === "deleted") {
                            location.reload(); // quick and simple refresh
                        } else {
                            alert("Error deleting trainer: " + data);
                        }
                    });
            }
        });
    </script>

</body>

</html>