<?php include "db.php"; ?>

<h2>👥 Users</h2>

<table border="1" cellpadding="8">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Username</th>
    <th>Phone</th>
</tr>

<?php
$q = $conn->query("SELECT * FROM users");
while($u = $q->fetch_assoc()){
    echo "<tr>
        <td>{$u['id']}</td>
        <td>{$u['name']}</td>
        <td>{$u['username']}</td>
        <td>{$u['phone']}</td>
    </tr>";
}
?>
</table>
