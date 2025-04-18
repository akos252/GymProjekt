<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'owner') {
    exit("Unauthorized");
}

$owner_id = $_SESSION['user_id'];

$gyms = $conn->prepare("SELECT * FROM gym WHERE owner_id = ?");
$gyms->bind_param("i", $owner_id);
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
            Edit
        </a><br>
        <button class="btn delete-gym-btn"
        data-gym-id="<?php echo $gym['gym_id']; ?>"
        style="margin-top: 10px; background: #a00; color: white; padding: 8px 12px; border: none; border-radius: 5px; cursor: pointer;">
        🗑️ Delete
        </button>
    </div>
<?php endwhile; ?>
