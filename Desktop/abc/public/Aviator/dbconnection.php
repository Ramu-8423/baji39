<?php

$servername = "localhost";
$username = "gamebridge_bagi39";
$password = "gamebridge_bagi39";
$dbname = "gamebridge_bagi39";

// Create connection  
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

