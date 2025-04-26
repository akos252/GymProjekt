<?php

function fetchGymDetails($conn, $gym_id) {
    $stmt = $conn->prepare("SELECT gym_name FROM gym WHERE gym_id = ?");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param("s", $gym_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function hasActiveMembership($conn, $user_id, $gym_id) {
    $stmt = $conn->prepare("
        SELECT membership_id FROM memberships 
        WHERE user_id = ? AND gym_id = ? AND end_date >= CURDATE()
    ");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("is", $user_id, $gym_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function purchaseMembership($conn, $user_id, $gym_id, $name, $duration_days) {
    $start_date = date("Y-m-d");
    $end_date = date("Y-m-d", strtotime("+$duration_days days"));

    $stmt = $conn->prepare("INSERT INTO memberships (user_id, gym_id, name, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param("issss", $user_id, $gym_id, $name, $start_date, $end_date);
    return $stmt->execute();
}
