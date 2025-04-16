<?php
session_start();
include "db.php";

// Fetch all gyms for dropdown selection
$gym_query = "SELECT gym_id, gym_name FROM gym";
$gym_result = $conn->query($gym_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $trainer_id = uniqid("T"); // Unique trainer ID
    $name = trim($_POST['name']);
    $time = trim($_POST['time']);
    $mobilenum = trim($_POST['mobilenum']);
    $gym_ids = $_POST['gym_ids']; // Array of selected gyms

    // Handle image upload as LONGBLOB
    $image = file_get_contents($_FILES['image']['tmp_name']);

    // Insert trainer into `trainer` table
    $trainer_sql = "INSERT INTO trainer (trainer_id, name, time, mobilenum, image) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($trainer_sql);
    $stmt->bind_param("sssss", $trainer_id, $name, $time, $mobilenum, $image);
    
    if ($stmt->execute()) {
        // Insert into `trainer_gym` table (trainer can work in multiple gyms)
        foreach ($gym_ids as $gym_id) {
            $trainer_gym_sql = "INSERT INTO trainer_gym (trainer_id, gym_id) VALUES (?, ?)";
            $stmt_gym = $conn->prepare($trainer_gym_sql);
            $stmt_gym->bind_param("ss", $trainer_id, $gym_id);
            $stmt_gym->execute();
        }

        echo "<script>alert('Trainer added successfully!'); window.location.href='gyms.php';</script>";
    } else {
        echo "<script>alert('Error adding trainer.'); window.location.href='add_trainer.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Trainer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div style="max-width: 600px; margin: auto; padding: 20px; background: #1e1e1e; border-radius: 10px;">
    <h2 style="color: red;">Add a Trainer</h2>
    
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Trainer Name" required><br>
        <input type="text" name="time" placeholder="Availability Time (e.g., 9 AM - 5 PM)" required><br>
        <input type="text" name="mobilenum" placeholder="Mobile Number" required><br>
        
        <h3 style="color: white;">Select Gym(s) They Work In</h3>
        <?php while ($gym = $gym_result->fetch_assoc()): ?>
            <label style="color: white;">
                <input type="checkbox" name="gym_ids[]" value="<?php echo $gym['gym_id']; ?>">
                <?php echo htmlspecialchars($gym['gym_name']); ?>
            </label><br>
        <?php endwhile; ?>

        <h3 style="color: white;">Upload Trainer Image</h3>
        <input type="file" name="image" accept="image/*" required><br>

        <button type="submit" style="background: red; color: white; padding: 10px; border-radius: 5px; cursor: pointer;">Add Trainer</button>
    </form>
</div>

</body>
</html>