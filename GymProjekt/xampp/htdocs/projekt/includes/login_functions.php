<?php

function loginUser($conn, $username, $password) {
    $username = trim($username);
    $password = trim($password);

    $stmt = $conn->prepare("SELECT id, username, user_type FROM login WHERE username = ? AND pwd = ?");
    if (!$stmt) {
        return "Database error.";
    }

    $stmt->bind_param("ss", $username, $password);
    
    if (!$stmt->execute()) {
        return "Database error during execute.";
    }

    $result = $stmt->get_result();
    if (!$result) {
        return "Database error getting result.";
    }

    if ($user = $result->fetch_assoc()) {
        return $user;
    } else {
        return "Invalid credentials";
    }
}
