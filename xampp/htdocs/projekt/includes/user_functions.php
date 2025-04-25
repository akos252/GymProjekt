<?php

function registerUser($conn, $username, $password, $confirm_password, $mobilenum, $dob) {
    $username = trim($username);
    $password = trim($password);
    $confirm_password = trim($confirm_password);
    $mobilenum = trim($mobilenum);
    $dob = trim($dob);

    if ($password !== $confirm_password) {
        return "Passwords do not match!";
    }

    $check_user = "SELECT * FROM login WHERE username = ? OR mobilenum = ?";
    $stmt = $conn->prepare($check_user);
    $stmt->bind_param("ss", $username, $mobilenum);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return "Username or mobile number already exists. Choose another!";
    }

    $insert_user = "INSERT INTO login (username, pwd, mobilenum, dob) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_user);
    $stmt->bind_param("ssss", $username, $password, $mobilenum, $dob);

    if ($stmt->execute()) {
        return true;
    } else {
        return "Registration failed. Try again!";
    }
}
