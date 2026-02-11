
<?php
// login_user.php
include "db.php";

session_start();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Chunguza kama user ipo
$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 1){
    $user = $result->fetch_assoc();
    if(password_verify($password, $user['password'])){
        // Password sahihi
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: menu.php"); // Page ya menu baada ya login
        exit();
    }else{
        header("Location: index.html?error=Password si sahihi");
        exit();
    }
}else{
    header("Location: index.html?error=Username haipo");
    exit();
}

$conn->close();
?>
