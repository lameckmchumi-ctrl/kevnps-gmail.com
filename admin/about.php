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
    <title>About | Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #4facfe; padding-bottom: 20px; }
        h1 { color: #333; font-size: 32px; margin-bottom: 10px; }
        .subtitle { color: #666; font-size: 16px; }
        
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 40px; }
        .info-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 8px; text-align: center; }
        .info-card .icon { font-size: 40px; margin-bottom: 15px; }
        .info-card .label { font-size: 14px; opacity: 0.9; margin-bottom: 10px; }
        .info-card .value { font-size: 18px; font-weight: bold; }
        
        .description { background: #f9f9f9; padding: 25px; border-radius: 8px; border-left: 4px solid #4facfe; margin-bottom: 30px; }
        .description h3 { color: #333; margin-bottom: 15px; }
        .description p { color: #666; line-height: 1.6; margin-bottom: 10px; }
        
        .back-link { margin-top: 20px; }
        .back-link a { color: #4facfe; text-decoration: none; font-weight: bold; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>ℹ️ About MCHUMI FOOD</h1>
        <p class="subtitle">Taarifa za Kampuni</p>
    </div>

    <div class="info-grid">
        <div class="info-card">
            <div class="icon">📍</div>
            <div class="label">Location / Mahali</div>
            <div class="value">Kinondoni</div>
        </div>

        <div class="info-card">
            <div class="icon">📱</div>
            <div class="label">Phone / Simu</div>
            <div class="value"><a href="tel:0618703787" style="color:white;text-decoration:none;">0618703787</a></div>
        </div>

        <div class="info-card">
            <div class="icon">✉️</div>
            <div class="label">Email</div>
            <div class="value"><a href="mailto:lameckmchumi@gmail.com" style="color:white;text-decoration:none;">lameckmchumi@gmail.com</a></div>
        </div>
    </div>

    <div class="description">
        <h3>🍽️ Kuhusu MCHUMI FOOD</h3>
        <p>
            MCHUMI FOOD ni mgeni wa mhudumu mwenye hiari ya kutoa chakula kizuri na cha kufanya haraka. 
            Tunatokea Kinondoni na tunakamatia wateja wa jiji kwa chakula chenyewe kizuri.
        </p>
        <p>
            Ufungaji wetu ni tayari 24/7 na tunakubali order kupitia kwa huduma hii. Wasiliana nasi kwa simu au email kwa swali lolote.
        </p>
    </div>

    <div class="back-link">
        <a href="dashboard.php">← Rudi Dashboard</a>
    </div>
</div>

</body>
</html>
