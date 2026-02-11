<?php
include "connect.php";

$user_id = $_POST['user_id'];
$total_price = $_POST['total_price'];
$status = "Pending";
$order_date = date("Y-m-d H:i:s");

$sql = "INSERT INTO orders (user_id, total_price, status, order_date)
        VALUES ('$user_id', '$total_price', '$status', '$order_date')";

if (mysqli_query($conn, $sql)) {
    echo "Order placed successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
