<?php
session_start();
include "db.php";

$error = "";

// Auto session cleanup (30 min inactivity)
$timeout_duration = 1800;
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout_duration) {
        session_unset();
        session_destroy();
        session_start();
    }
}
$_SESSION['last_activity'] = time();

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = "Jaza username na password";
    } else {

        // 1️⃣ Check Admin
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM admins WHERE username=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($res && mysqli_num_rows($res) === 1) {
            $admin = mysqli_fetch_assoc($res);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['role'] = 'admin';
                $_SESSION['last_activity'] = time();
                header("Location: admin/dashboard.php");
                exit();
            }
        }

        // 2️⃣ Check User
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($res && mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);
            if (password_verify($password, $user['password'])) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = 'user';
                $_SESSION['last_activity'] = time();
                header("Location: user/dashboard.php");
                exit();
            }
        }

        $error = "Username au password sio sahihi";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<!-- Show error if any -->
<?php if ($error): ?>
    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<!-- ✅ Login form ipo kila wakati, haiondoki -->
<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Login</button>
</form>

<br>
<a href="index.html">⬅ Rudi Home</a>

</body>
</html>
