<?php
session_start();
include "db.php";

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | Admin</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
        
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #4facfe; padding-bottom: 15px; }
        h1 { color: #333; }
        .user-info { color: #666; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #4facfe; color: white; padding: 12px; text-align: left; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        tr:hover { background: #f9f9f9; }
        
        .status-pending { background: #fff3cd; color: #856404; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .status-preparing { background: #cfe2ff; color: #084298; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .status-delivered { background: #d1e7dd; color: #0f5132; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        .status-cancelled { background: #f8d7da; color: #842029; padding: 5px 10px; border-radius: 3px; font-weight: bold; }
        
        .action-form { display: flex; gap: 5px; align-items: center; }
        select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        button { padding: 8px 15px; background: #4facfe; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; }
        button:hover { background: #00f2fe; }
        
        .items { font-size: 13px; color: #666; margin-top: 5px; }
        .back-link { margin-top: 20px; }
        .back-link a { color: #4facfe; text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>📦 Manage Orders</h1>
        <div class="user-info">
            Karibu: <strong><?= htmlspecialchars($username) ?></strong>
            | <a href="logout.php" style="color:#4facfe;text-decoration:none;">Logout</a>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT o.id, o.user_id, o.total_price, o.status, o.order_date, u.name, u.username
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    ORDER BY o.order_date DESC";
            
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($order = $result->fetch_assoc()) {
                    // Get order items
                    $items_sql = "SELECT item_name, quantity, price FROM order_items WHERE order_id = ?";
                    $items_stmt = $conn->prepare($items_sql);
                    $items_stmt->bind_param("i", $order['id']);
                    $items_stmt->execute();
                    $items_result = $items_stmt->get_result();
                    
                    $items_text = "";
                    while ($item = $items_result->fetch_assoc()) {
                        $items_text .= "{$item['item_name']} x{$item['quantity']} ({$item['price']} Tsh)<br>";
                    }
                    
                    // Status color class
                    $status = strtolower($order['status']);
                    $status_class = "status-" . str_replace(' ', '-', $status);
                    
                    echo "<tr>
                        <td>#{$order['id']}</td>
                        <td>
                            <strong>{$order['name']}</strong><br>
                            <small>@{$order['username']}</small>
                        </td>
                        <td>
                            <div class='items'>{$items_text}</div>
                        </td>
                        <td>{$order['total_price']} Tsh</td>
                        <td>" . date('d-m-Y H:i', strtotime($order['order_date'])) . "</td>
                        <td><span class='{$status_class}'>{$order['status']}</span></td>
                        <td>
                            <form method='POST' action='update_order_status.php' class='action-form'>
                                <input type='hidden' name='id' value='{$order['id']}'>
                                <select name='status' required>
                                    <option value='Pending' " . ($order['status'] === 'Pending' ? 'selected' : '') . ">Pending</option>
                                    <option value='Preparing' " . ($order['status'] === 'Preparing' ? 'selected' : '') . ">Preparing</option>
                                    <option value='Delivered' " . ($order['status'] === 'Delivered' ? 'selected' : '') . ">Delivered</option>
                                    <option value='Cancelled' " . ($order['status'] === 'Cancelled' ? 'selected' : '') . ">Cancelled</option>
                                </select>
                                <button type='submit'>Update</button>
                            </form>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='text-align:center;color:#999;padding:30px;'>No orders yet</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="back-link">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
