<?php
session_start();
include "db.php";
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $member_id = $_POST['member_id'];
    $name = $_POST['name'];
    $dob = $_POST['dob'];
    $age = $_POST['age'];
    $package = $_POST['package'];
    $mobilenum = $_POST['mobilenum'];
    $trainer_id = $_POST['trainer_id'];

    $sql = "INSERT INTO member (member_id, name, dob, age, package, mobilenum, trainer_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisss", $member_id, $name, $dob, $age, $package, $mobilenum, $trainer_id);
    $stmt->execute();
}

// Fetch members
$members = $conn->query("SELECT * FROM member");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Members</title>
</head>
<body>
    <h2>Members List</h2>
    <table border="1">
        <tr>
            <th>ID</th><th>Name</th><th>DOB</th><th>Age</th><th>Package</th><th>Mobile</th><th>Trainer</th>
        </tr>
        <?php while ($row = $members->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['member_id']; ?></td>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['dob']; ?></td>
                <td><?php echo $row['age']; ?></td>
                <td><?php echo $row['package']; ?></td>
                <td><?php echo $row['mobilenum']; ?></td>
                <td><?php echo $row['trainer_id']; ?></td>
            </tr>
        <?php } ?>
    </table>

    <h2>Add Member</h2>
    <form method="post">
        <input type="text" name="member_id" placeholder="Member ID" required><br>
        <input type="text" name="name" placeholder="Name" required><br>
        <input type="date" name="dob" required><br>
        <input type="number" name="age" placeholder="Age" required><br>
        <input type="text" name="package" placeholder="Package" required><br>
        <input type="text" name="mobilenum" placeholder="Mobile" required><br>
        <input type="text" name="trainer_id" placeholder="Trainer ID"><br>
        <button type="submit" name="add">Add Member</button>
    </form>
</body>
</html>