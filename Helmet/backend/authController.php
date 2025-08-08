<?php
require_once 'db.php';

function registerUser($username, $email, $password) {
    global $conn;
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hash);
    return $stmt->execute();
}

function loginUser($email, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $username, $hash);
        $stmt->fetch();
        if (password_verify($password, $hash)) {
            return ['id' => $id, 'username' => $username];
        }
    }
    return false;
}
