<?php

function loginUser($conn, $username, $password) {
    $username = trim($username);
    $password = trim($password);

    $stmt = $conn->prepare("SELECT id, username, user_type FROM login WHERE username = ? AND pwd = ?");
    if (!$stmt) {
        return "Database error.";
    }

    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        return $user;
    } else {
        return "Invalid credentials";
    }
}
