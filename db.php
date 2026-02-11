<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "mchumi_food");

// Angalia kama kuna error
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
