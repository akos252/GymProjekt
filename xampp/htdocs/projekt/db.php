<?php
// db.php - adatbázis kapcsolat
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gymdb';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Connection Failed: ' . $conn->connect_error);
}
?>
