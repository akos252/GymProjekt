<?php

function getInsertId($conn) {
    return $conn->insert_id;
}

function registerOwner($conn, $username, $password, $confirm_password, $fullname, $email, $mobilenum, $dob, $mockInsertId = null) {
    $username = trim($username);
    $password = trim($password);
    $confirm_password = trim($confirm_password);
    $fullname = trim($fullname);
    $email = trim($email);
    $mobilenum = trim($mobilenum);
    $dob = trim($dob);

    if ($password !== $confirm_password) {
        return "Passwords do not match!";
    }

    // Check if username or mobile already exists
    $check_user = "SELECT * FROM login WHERE username = ? OR mobilenum = ?";
    $stmt = $conn->prepare($check_user);
    if (!$stmt) {
        return "Error preparing SELECT user.";
    }
    $stmt->bind_param("ss", $username, $mobilenum);

    if (!$stmt->execute()) {
        return "Database error during SELECT execute.";
    }

    $result = $stmt->get_result();
    if (!$result) {
        return "Database error getting SELECT result.";
    }

    if ($result->num_rows > 0) {
        return "Username or mobile number already exists.";
    }

    // Insert into login table
    $insert_login = "INSERT INTO login (username, pwd, mobilenum, dob, user_type) VALUES (?, ?, ?, ?, 'owner')";
    $stmt = $conn->prepare($insert_login);
    if (!$stmt) {
        return "Error preparing INSERT login.";
    }
    $stmt->bind_param("ssss", $username, $password, $mobilenum, $dob);

    if (!$stmt->execute()) {
        return "Registration failed at login insertion.";
    }

    // Get the new owner ID
    if ($mockInsertId !== null) {
        $owner_id = $mockInsertId;
    } else {
        $owner_id = $conn->insert_id;
    }

    // Insert into owner table
    $insert_owner = "INSERT INTO owner (owner_id, full_name, email, contact_number) VALUES (?, ?, ?, ?)";
    $stmt_owner = $conn->prepare($insert_owner);
    if (!$stmt_owner) {
        return "Error preparing INSERT owner.";
    }
    $stmt_owner->bind_param("isss", $owner_id, $fullname, $email, $mobilenum);

    if ($stmt_owner->execute()) {
        return true;
    } else {
        return "Registration failed at owner insertion.";
    }
}
