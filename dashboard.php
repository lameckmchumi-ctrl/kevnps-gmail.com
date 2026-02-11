<?php
session_start();

// Hakikisha user amelogin
if (!isset($_SESSION['username'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - MCHUMI FOOD</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0f0f0;
            text-align: center;
            padding: 50px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
        }
        a.logout {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: red;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Karibu, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Hii ni dashboard yako.</p>
        <a class="logout" href="logout.php">Logout</a>
    </div>
</body>
</html>
