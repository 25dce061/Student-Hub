<?php
// ==============================================================================
// StudentHub - MySQL Database Connection Configuration
// ==============================================================================

$host = "localhost";
$user = "root";
$password = "";
$database = "studenthub";

// Create connection using MySQLi
$conn = new mysqli($host, $user, $password, $database);

// Check if connection was successful
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
