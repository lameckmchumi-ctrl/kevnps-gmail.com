<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Manage Menu Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        
        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header h2 {
            margin: 0;
            color: #333;
        }
        
        .btn {
            padding: 10px 20px;
            background: #4facfe;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #00f2fe;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #4facfe;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .edit-btn, .delete-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }
        
        .edit-btn {
            background: #22c55e;
            color: white;
        }
        
        .delete-btn {
            background: #ef4444;
            color: white;
        }
        
        .edit-btn:hover {
            background: #16a34a;
        }
        
        .delete-btn:hover {
            background: #dc2626;
        }
        
        .price {
            color: #4facfe;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php 
include "db.php"; 
include "auth.php"; 
?>

<div class="header">
    <h2>📋 Manage Menu Items</h2>
    <a href="add_menu.php" class="btn">➕ Add Item</a>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Item Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = $conn->query("SELECT mi.*, mc.category_name FROM menu_items mi 
                              LEFT JOIN menu_categories mc ON mi.category_id = mc.id 
                              ORDER BY mi.category_id, mi.item_name");
            
            if($q && $q->num_rows > 0) {
                while($m = $q->fetch_assoc()){
                    $image_html = '';
                    if(isset($m['image']) && !empty($m['image']) && file_exists("../user/images/" . $m['image'])) {
                        $image_html = '<img src="../user/images/' . htmlspecialchars($m['image']) . '" class="item-image" alt="' . htmlspecialchars($m['item_name']) . '">';
                    } else {
                        $image_html = '<div style="width:80px; height:80px; background:#ddd; border-radius:5px; display:flex; align-items:center; justify-content:center; color:#999;">🍽️</div>';
                    }
                    
                    $category_name = $m['category_name'] ?? 'Unknown';
                    
                    echo "<tr>
                        <td>{$image_html}</td>
                        <td><strong>{$m['item_name']}</strong></td>
                        <td class='price'>" . number_format($m['price']) . " Tsh</td>
                        <td>{$category_name}</td>
                        <td>
                            <div class='actions'>
                                <a href='edit_menu.php?id={$m['id']}' class='edit-btn'>✏️ Edit</a>
                                <a href='delete_menu.php?id={$m['id']}' class='delete-btn' onclick=\"return confirm('Delete this item?')\">🗑️ Delete</a>
                            </div>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center; color:#999;'>No menu items found. <a href='add_menu.php'>Add one now</a></td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>
