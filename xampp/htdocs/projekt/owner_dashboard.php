<?php
session_start();
include "db.php";

// Restrict access to owners only
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'owner') {
    die("Access denied. Owners only.");
}

$owner_id = $_SESSION['user_id'];

// Handle Add Gym
if (isset($_POST['add_gym'])) {
    $gym_name = trim($_POST['gym_name']);
    $gym_address = trim($_POST['gym_address']);
    $gym_type = trim($_POST['gym_type']);
    $gym_id = uniqid("G");

    $sql = "INSERT INTO gym (gym_id, gym_name, gym_address, gym_type, owner_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $gym_id, $gym_name, $gym_address, $gym_type, $owner_id);
    $stmt->execute();
}

// Handle Add Trainer
if (isset($_POST['add_trainer'])) {
    $trainer_id = uniqid("T");
    $name = trim($_POST['trainer_name']);
    $time = trim($_POST['trainer_time']);
    $mobilenum = trim($_POST['trainer_mobilenum']);
    $image = file_get_contents($_FILES['trainer_image']['tmp_name']);
    $gym_ids = $_POST['assigned_gyms'];

    $insert = "INSERT INTO trainer (trainer_id, name, time, mobilenum, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert);
    $stmt->bind_param("sssss", $trainer_id, $name, $time, $mobilenum, $image);
    if ($stmt->execute()) {
        foreach ($gym_ids as $gid) {
            $map = "INSERT INTO trainer_gym (trainer_id, gym_id) VALUES (?, ?)";
            $s = $conn->prepare($map);
            $s->bind_param("ss", $trainer_id, $gid);
            $s->execute();
        }
    }
}

// Fetch owner's gyms
$gyms = $conn->prepare("SELECT * FROM gym WHERE owner_id = ?");
$gyms->bind_param("i", $owner_id);
$gyms->execute();
$gyms_result = $gyms->get_result();

// For trainer form: fetch gyms to assign to
$gym_options = $conn->prepare("SELECT gym_id, gym_name FROM gym WHERE owner_id = ?");
$gym_options->bind_param("i", $owner_id);
$gym_options->execute();
$gym_data = $gym_options->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Owner Dashboard - Gym Management</title>
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
        <a href="#">Placeholder</a>
        <a href="#">Placeholder</a>
    </div>
    <div class="nav-right">
        <a href="logout.php" class="btn">Logout</a>
    </div>
</nav>

<div class="container" style="max-width: 900px; margin: auto;">
    <h2 style="color: red;">Welcome, Gym Owner</h2>

    <!-- ADD GYM -->
    <div style="background: #222; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
        <h3>Add a New Gym</h3>
        <form method="post">
            <input type="text" name="gym_name" placeholder="Gym Name" required><br>
            <input type="text" name="gym_address" placeholder="Address" required><br>
            <input type="text" name="gym_type" placeholder="Gym Type (e.g. CrossFit, Boxing)" required><br>
            <button class="btn" type="submit" name="add_gym">Add Gym</button>
        </form>
    </div>

    <!-- ADD TRAINER -->
    <div style="background: #222; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
        <h3>Add a Trainer</h3>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="trainer_name" placeholder="Trainer Name" required><br>
            <input type="text" name="trainer_time" placeholder="Availability Time" required><br>
            <input type="text" name="trainer_mobilenum" placeholder="Mobile Number" required><br>
            <input type="file" name="trainer_image" accept="image/*" required><br>
            <label style="color: white;">Assign to Gyms:</label><br>
            <?php while ($g = $gym_data->fetch_assoc()): ?>
                <label style="color: white;">
                    <input type="checkbox" name="assigned_gyms[]" value="<?php echo $g['gym_id']; ?>">
                    <?php echo htmlspecialchars($g['gym_name']); ?>
                </label><br>
            <?php endwhile; ?>
            <button class="btn" type="submit" name="add_trainer">Add Trainer</button>
        </form>
    </div>

    <!-- LIST GYMS -->
    <h3>My Gyms</h3>
    <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-around;">
        <?php while ($gym = $gyms_result->fetch_assoc()): ?>
            <div style="background: #1e1e1e; padding: 20px; border-radius: 10px; width: 40%; text-align: center;">
                <h3 style="color: red;"><?php echo htmlspecialchars($gym['gym_name']); ?></h3>
                <p style="font-weight: bold;"><?php echo htmlspecialchars($gym['gym_type']); ?></p>
                <p style="color: #bbb;"><?php echo htmlspecialchars($gym['gym_address']); ?></p>
                <a href="view_gym.php?gym_id=<?php echo $gym['gym_id']; ?>" 
                   style="display: inline-block; margin-top: 10px; background: red; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px;">
                    View Trainers
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>