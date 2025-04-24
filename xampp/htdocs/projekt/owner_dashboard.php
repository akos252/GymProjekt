<?php
session_start();
include "db.php";


// Restrict access to owners only
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'owner') {
    die("Access denied. Owners only.");
}

$owner_id = $_SESSION['user_id'];

// Handle Add Gym
if (isset($_POST['gym_name']) && isset($_POST['ajax'])) {
    $gym_name = trim($_POST['gym_name']);
    $gym_address = trim($_POST['gym_address']);
    $gym_type = trim($_POST['gym_type']);
    $gym_id = uniqid("G");

    $check = $conn->prepare("SELECT * FROM gym WHERE gym_name = ? AND owner_id = ?");
    $check->bind_param("si", $gym_name, $owner_id);
    $check->execute();
    $exists = $check->get_result();

    if ($exists->num_rows > 0) {
        echo "<p style='color: orange;'>This gym already exists.</p>";
        exit();
    }

    $sql = "INSERT INTO gym (gym_id, gym_name, gym_address, gym_type, owner_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $gym_id, $gym_name, $gym_address, $gym_type, $owner_id);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>Gym added successfully ✅</p>";
    } else {
        echo "<p style='color: red;'>Failed to add gym ❌</p>";
    }

    exit(); // prevent page content from loading after AJAX
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

// Handle update trainer mobile number
if (isset($_POST['update_trainer_btn'])) {
    $trainer_id = $_POST['update_trainer_id'];
    $new_mobilenum = trim($_POST['update_mobilenum']);

    // Make sure trainer belongs to one of owner's gyms
    $verify = $conn->prepare("
        SELECT 1 FROM trainer_gym tg
        JOIN gym g ON g.gym_id = tg.gym_id
        WHERE tg.trainer_id = ? AND g.owner_id = ?
    ");
    $verify->bind_param("si", $trainer_id, $owner_id);
    $verify->execute();
    $result = $verify->get_result();

    if ($result->num_rows > 0) {
        $update = $conn->prepare("UPDATE trainer SET mobilenum = ? WHERE trainer_id = ?");
        $update->bind_param("ss", $new_mobilenum, $trainer_id);
        $update->execute();
    }
}

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

    <div class="container" style="max-width: 900px; margin: auto;">
        <h2 style="color: red;">Welcome, Gym Owner</h2>

        <!-- ADD GYM -->
        <div style="background: #222; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
            <h3>Add a New Gym</h3>
            <form id="add-gym-form">
                <input type="text" name="gym_name" placeholder="Gym Name" required><br>
                <input type="text" name="gym_address" placeholder="Address" required><br>
                <input type="text" name="gym_type" placeholder="Gym Type (e.g. CrossFit, Boxing)" required><br>
                <input type="hidden" name="ajax" value="1">
                <button class="btn" type="submit">Add Gym</button>
            </form>

            <div id="add-gym-response" style="margin-top: 10px;"></div>
        </div>

        <!-- ADD TRAINER -->
        <div style="background: #222; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
            <h3>Add a Trainer</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="trainer_name" placeholder="Trainer Name" required><br>
                <input type="text" name="trainer_time" placeholder="Availability Time" required><br>
                <input type="text" name="trainer_mobilenum" placeholder="Mobile Number" required><br>
                <input type="file" name="trainer_image" accept="image/*" required><br>
                <label style="color: white;">Assign to Gyms:</label>
                <div class="checkbox-grid">
                    <?php while ($g = $gym_data->fetch_assoc()): ?>
                    <label class="styled-checkbox">
                        <input type="checkbox" name="assigned_gyms[]" value="<?php echo $g['gym_id']; ?>">
                        <span><?php echo htmlspecialchars($g['gym_name']); ?></span>
                    </label>
                    <?php endwhile; ?>
                </div>
                <button class="btn" type="submit" name="add_trainer">Add Trainer</button>
            </form>
        </div>

        <!-- LIST GYMS -->
        <h3>My Gyms</h3>
        <div id="gym-list">
            <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-around;">
                <?php
        $gyms->execute();
        $gyms_result = $gyms->get_result();
        while ($gym = $gyms_result->fetch_assoc()): ?>
                <div style="background: #1e1e1e; padding: 20px; border-radius: 10px; width: 40%; text-align: center;">
                    <h3 style="color: red;"><?php echo htmlspecialchars($gym['gym_name']); ?></h3>
                    <p style="font-weight: bold;"><?php echo htmlspecialchars($gym['gym_type']); ?></p>
                    <p style="color: #bbb;"><?php echo htmlspecialchars($gym['gym_address']); ?></p>
                    <a href="view_gym.php?gym_id=<?php echo $gym['gym_id']; ?>"
                        style="display: inline-block; margin-top: 10px; background: red; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px;">
                        View Trainers
                    </a>
                    <a href="edit_gym.php?gym_id=<?php echo $gym['gym_id']; ?>"
                        style="display: inline-block; margin-top: 10px; background: #555; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px;">
                        ✏️ Edit
                    </a><br>
                    <button class="btn delete-gym-btn" data-gym-id="<?php echo $gym['gym_id']; ?>"
                        style="margin-top: 10px; background: #a00; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer;">
                        🗑️ Delete
                    </button>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <!-- LIST TRAINERS -->
        <h3 style="margin-top: 40px; text-align: center;">My Trainers</h3>
        <div
            style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; max-width: 800px; margin: 0 auto;">
            <?php
$trainers = $conn->prepare("
    SELECT DISTINCT t.trainer_id, t.name
    FROM trainer t
    JOIN trainer_gym tg ON tg.trainer_id = t.trainer_id
    JOIN gym g ON g.gym_id = tg.gym_id
    WHERE g.owner_id = ?
");
$trainers->bind_param("i", $owner_id);
$trainers->execute();
$trainers_result = $trainers->get_result();

if ($trainers_result->num_rows > 0):
    while ($trainer = $trainers_result->fetch_assoc()):
?>
            <div
                style="width: 40%; min-width: 250px; background: #1e1e1e; padding: 15px; border-radius: 8px; text-align: center;">
                <a href="view_trainer.php?trainer_id=<?php echo $trainer['trainer_id']; ?>"
                    style="text-decoration: none; color: red; font-weight: bold; font-size: 16px;">
                    <?php echo htmlspecialchars($trainer['name']); ?>
                </a>
            </div>
            <?php endwhile; else: ?>
            <p style="color: gray;">No trainers found.</p>
            <?php endif; ?>
        </div>
    </div>
    </div>


    <script>
// Load all gyms
function loadGyms() {
    fetch("api/gyms.php")
        .then(res => res.json())
        .then(data => {
            const container = document.getElementById("gym-list");
            container.innerHTML = "";

            const wrapper = document.createElement("div");
            wrapper.style = "display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-around;";

            data.forEach(gym => {
                const card = document.createElement("div");
                card.style = `
                    background: #1e1e1e;
                    padding: 20px;
                    border-radius: 10px;
                    width: 40%;
                    text-align: center;
                `;

                card.innerHTML = `
                    <h3 style="color: red;">${gym.gym_name}</h3>
                    <p style="font-weight: bold;">${gym.gym_type}</p>
                    <p style="color: #bbb;">${gym.gym_address}</p>

                    <a href="view_gym.php?gym_id=${gym.gym_id}" 
                       style="display: inline-block; margin-top: 10px; background: red; color: white; padding: 8px 12px; text-decoration: none; border-radius: 5px;">
                        View Trainers
                    </a>

                    <button onclick="editGym('${gym.gym_id}', '${gym.gym_name}', '${gym.gym_address}', '${gym.gym_type}')"
                            style="margin-top: 10px; background: #555; color: white; padding: 8px 12px; border-radius: 5px; border: none; cursor: pointer;">
                        ✏️ Edit
                    </button><br>

                    <button class="btn delete-gym-btn"
                        data-gym-id="${gym.gym_id}"
                        style="margin-top: 10px; background: #a00; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer;">
                        🗑️ Delete
                    </button>
                `;

                wrapper.appendChild(card);
            });

            container.appendChild(wrapper);
        });
}

// Add gym
document.getElementById("add-gym-form").addEventListener("submit", function (e) {
    e.preventDefault();

    const form = e.target;
    const data = {
        gym_name: form.gym_name.value,
        gym_address: form.gym_address.value,
        gym_type: form.gym_type.value
    };

    fetch("api/gyms.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === "success") {
            form.reset();
            loadGyms();
        } else {
            alert(response.error || "Failed to add gym.");
        }
    });
});

// Delete gym
document.getElementById("gym-list").addEventListener("click", function (e) {
    if (e.target && e.target.classList.contains("delete-gym-btn")) {
        const gymId = e.target.dataset.gymId;

        if (!confirm("Are you sure you want to delete this gym?")) return;

        fetch("api/gyms.php", {
            method: "DELETE",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({ gym_id: gymId })
        })
        .then(res => res.json())
        .then(response => {
            if (response.status === "deleted") {
                loadGyms();
            } else {
                alert(response.error || "Failed to delete gym.");
            }
        });
    }
});

// Edit gym
function editGym(gym_id, name, address, type) {
    const newName = prompt("Edit Gym Name", name);
    const newAddress = prompt("Edit Address", address);
    const newType = prompt("Edit Type", type);

    if (newName && newAddress && newType) {
        fetch("api/gyms.php", {
            method: "PUT",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                gym_id,
                gym_name: newName,
                gym_address: newAddress,
                gym_type: newType
            })
        })
        .then(res => res.json())
        .then(response => {
            if (response.status === "updated") {
                loadGyms();
            } else {
                alert(response.error || "Failed to update gym.");
            }
        });
    }
}

// Initial load
loadGyms();
</script>

</body>

</html>