<?php
include "db.php"; include "auth.php";
$conn->query("DELETE FROM menu_items WHERE id=".$_GET['id']);
header("Location: menu.php");
