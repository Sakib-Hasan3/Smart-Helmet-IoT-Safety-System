<?php
// backend/db.php

$host = 'localhost';
$user = 'root';
$password = ''; // set your MySQL password if needed
$dbname = 'smart_helmet';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
