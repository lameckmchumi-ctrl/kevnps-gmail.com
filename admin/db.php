<?php
$conn = new mysqli("localhost","root","","mchumi_food");
if($conn->connect_error){
    die("DB Error");
}

// Only start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
