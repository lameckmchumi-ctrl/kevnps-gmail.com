<?php
session_start();
include "db.php";

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    
    // Validate status
    $valid_statuses = ['Pending', 'Preparing', 'Delivered', 'Cancelled'];
    if (!in_array($status, $valid_statuses)) {
        $status = 'Pending';
    }
    
    // Use prepared statement for security
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
}

header("Location: orders.php");
exit();
?>
