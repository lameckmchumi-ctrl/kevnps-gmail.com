<?php
session_start();
include "db.php";

if(!isset($_SESSION['user'])){
    die("Not logged in");
}

$data = json_decode(file_get_contents("php://input"), true);
$username = $_SESSION['user'];
$total = $data['total'];

$conn->query("INSERT INTO orders(username,total) VALUES('$username','$total')");
$order_id = $conn->insert_id;

foreach($data['orders'] as $name=>$item){
    $price = $item['price'];
    $qty = $item['qty'];

    $conn->query("INSERT INTO order_items(order_id,item_name,price,quantity)
                  VALUES('$order_id','$name','$price','$qty')");
}

echo "ORDER IMEHIFADHIWA VIZURI ✅";
