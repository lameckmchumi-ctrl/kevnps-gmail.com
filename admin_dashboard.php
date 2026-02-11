<?php
session_start();
if(!isset($_SESSION['loggedin']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Karibu Admin, <?php echo htmlspecialchars($username); ?>!</h2>
    <h3>Manage Orders / Menu / Users</h3>
    <p>Hapa utakuwa na links au functionalities za admin</p>
</body>
</html>
