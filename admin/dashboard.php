<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        
        .container { max-width: 900px; width: 100%; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #4facfe; padding-bottom: 20px; }
        h1 { color: #333; font-size: 32px; }
        .welcome { color: #666; font-size: 14px; margin-top: 10px; }
        
        .menu { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .menu-item { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; text-align: center; text-decoration: none; transition: transform 0.3s, box-shadow 0.3s; }
        .menu-item:hover { transform: translateY(-5px); box-shadow: 0 5px 20px rgba(0,0,0,0.2); }
        .menu-item .icon { font-size: 40px; margin-bottom: 10px; }
        .menu-item .title { font-size: 18px; font-weight: bold; }
        
        .logout { text-align: center; margin-top: 30px; }
        .logout a { background: #ef4444; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; transition: background 0.3s; }
        .logout a:hover { background: #dc2626; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>👑 Admin Dashboard</h1>
        <p class="welcome">Karibu, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></p>
    </div>

    <div class="menu">
        <a href="orders.php" class="menu-item">
            <div class="icon">📦</div>
            <div class="title">Manage Orders</div>
        </a>
        <a href="menu.php" class="menu-item">
            <div class="icon">🍽️</div>
            <div class="title">Manage Menu</div>
        </a>
        <a href="add_menu.php" class="menu-item">
            <div class="icon">➕</div>
            <div class="title">Add Menu Item</div>
        </a>
        <a href="users.php" class="menu-item">
            <div class="icon">👥</div>
            <div class="title">View Users</div>
        </a>
    </div>

    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; margin-top: 30px;">
        <h2 style="margin-bottom: 20px;">ℹ️ MCHUMI FOOD - Taarifa</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 6px;">
                <div style="font-size: 28px; margin-bottom: 10px;">📍</div>
                <div style="font-size: 12px; opacity: 0.9;">Location / Mahali</div>
                <div style="font-size: 18px; font-weight: bold; margin-top: 5px;">Kinondoni</div>
            </div>
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 6px;">
                <div style="font-size: 28px; margin-bottom: 10px;">📱</div>
                <div style="font-size: 12px; opacity: 0.9;">Phone / Simu</div>
                <div style="font-size: 18px; font-weight: bold; margin-top: 5px;">
                    <a href="tel:0618703787" style="color:white;text-decoration:none;">0618703787</a>
                </div>
            </div>
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 6px;">
                <div style="font-size: 28px; margin-bottom: 10px;">✉️</div>
                <div style="font-size: 12px; opacity: 0.9;">Email</div>
                <div style="font-size: 16px; font-weight: bold; margin-top: 5px;">
                    <a href="mailto:lameckmchumi@gmail.com" style="color:white;text-decoration:none;">lameckmchumi@gmail.com</a>
                </div>
            </div>
        </div>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>
</div>

</body>
</html>
