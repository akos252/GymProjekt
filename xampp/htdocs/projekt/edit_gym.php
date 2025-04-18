<?php
session_start();
include "db.php";

// Check if user is an owner
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'owner') {
    die("Access denied.");
}

$owner_id = $_SESSION['user_id'];

// Check if gym_id is provided
if (!isset($_GET['gym_id'])) {
    die("No gym selected.");
}

$gym_id = $_GET['gym_id'];

// Fetch gym and validate ownership
$sql = "SELECT * FROM gym WHERE gym_id = ? AND owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $gym_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Gym not found or not owned by you.");
}

$gym = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['gym_name']);
    $new_address = trim($_POST['gym_address']);
    $new_type = trim($_POST['gym_type']);

    $update = "UPDATE gym SET gym_name = ?, gym_address = ?, gym_type = ? WHERE gym_id = ? AND owner_id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssssi", $new_name, $new_address, $new_type, $gym_id, $owner_id);

    if ($stmt->execute()) {
        if (isset($_POST['ajax'])) {
            echo "<p style='color: green;'>Changes saved successfully ✅</p>";
            exit();
        } else {
            header("Location: owner_dashboard.php?updated=1");
            exit();
        }
    } else {
        if (isset($_POST['ajax'])) {
            echo "<p style='color: red;'>Update failed ❌</p>";
            exit();
        } else {
            $error = "Failed to update gym.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Gym - Gym Management</title>
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
        <a href="owner_dashboard.php">Dashboard</a>
        <a href="gyms.php">View Gyms</a>
    </div>
    <div class="nav-right">
        <a href="logout.php" class="btn">Logout</a>
    </div>
</nav>

<div class="container" style="max-width: 600px; margin: auto;">
    <h2 style="color: red;">Edit Gym</h2>

    <form id="edit-gym-form">
    <input type="text" name="gym_name" placeholder="Gym Name" value="<?php echo htmlspecialchars($gym['gym_name']); ?>" required><br>
    <input type="text" name="gym_address" placeholder="Address" value="<?php echo htmlspecialchars($gym['gym_address']); ?>" required><br>
    <input type="text" name="gym_type" placeholder="Type (e.g. CrossFit, Boxing)" value="<?php echo htmlspecialchars($gym['gym_type']); ?>" required><br>
    <input type="hidden" name="ajax" value="1"> <!-- let PHP know it's AJAX -->
    <button class="btn" type="submit">Save Changes</button>
</form>

<div id="response-msg" style="margin-top: 10px;"></div>

    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <p><a href="owner_dashboard.php" style="color: red;">&larr; Back to Dashboard</a></p>
</div>
<script>
document.getElementById("edit-gym-form").addEventListener("submit", function(e) {
    e.preventDefault(); // prevent regular form submission

    const form = e.target;
    const formData = new FormData(form);

    fetch("edit_gym.php?gym_id=<?php echo $gym_id; ?>", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("response-msg").innerHTML = data;
    })
    .catch(err => {
        document.getElementById("response-msg").innerHTML = "<p style='color:red;'>An error occurred.</p>";
    });
});
</script>
</body>
</html>