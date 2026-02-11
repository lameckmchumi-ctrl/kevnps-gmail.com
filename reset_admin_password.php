<?php
include __DIR__ . '/db.php';

header('Content-Type: text/html; charset=utf-8');
echo "<style>body{font-family:Arial;padding:20px}pre{background:#f0f0f0;padding:10px;border-radius:5px}</style>";

// Hash a test password
$test_password = 'admin123';
$hashed = password_hash($test_password, PASSWORD_BCRYPT);

echo "<h2>Kuongeza/Kubadilisha Admin Password</h2>";
echo "<p>SQL kubadilusha password wa admin ipo kuwa 'admin123':</p>";
echo "<pre>UPDATE admins SET password = '$hashed' WHERE username = 'admin';</pre>";

echo "<p><strong>Baada ya ku-run SQL:</strong></p>";
echo "<pre>Username: admin\nPassword: admin123</pre>";

echo "<p>Unaweza kuandika SQL hii kwenye phpmyadmin SQL tab au kutengeneza script ikiwa inavyoandikwa hapa:</p>";
?>
