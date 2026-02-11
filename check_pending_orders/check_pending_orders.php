<?php
include "db.php";
include "auth.php";

$q = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='Pending'");
$r = $q->fetch_assoc();

echo json_encode([
    "pending" => $r['total']
]);
