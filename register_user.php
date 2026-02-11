<?php
// register_user.php
include "db.php";

// Pata data kutoka fomu
$name     = $_POST['name'] ?? '';
$phone    = $_POST['phone'] ?? '';
$district = $_POST['district'] ?? '';
$ward     = $_POST['ward'] ?? '';
$street   = $_POST['street'] ?? '';
$house_no = $_POST['house_no'] ?? '';
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Hash password kwa usalama
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check kama username tayari ipo
$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
    // Username tayari imejengwa
    header("Location: register.php?error=Username tayari imejengwa");
    exit();
}

// Ingiza user mpya
$stmt = $conn->prepare("INSERT INTO users (name, phone, district, ward, street, house_no, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $name, $phone, $district, $ward, $street, $house_no, $username, $hashed_password);

if($stmt->execute()){
    header("Location: register.php?success=Registration imefanikiwa");
}else{
    header("Location: register.php?error=Tatizo limejitokeza");
}

$stmt->close();
$conn->close();
?>
