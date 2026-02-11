<?php
session_start();
include "db.php";

$username = $_SESSION['username'] ?? 'guest';

// Search term from GET
$search = trim($_GET['q'] ?? '');
$search_sql = '';
if($search !== ''){
    $search_esc = $conn->real_escape_string($search);
    $search_sql = " AND item_name LIKE '%" . $search_esc . "%' ";
}

// Handle Save Order
if(isset($_POST['save_order'])){
    $items = $_POST['items'] ?? '[]';
    $total = $_POST['total'] ?? 0;

    $items_array = json_decode($items, true);
    
    if(!empty($items_array) && $total > 0) {
        // Get user_id from username
        $user_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $user_stmt->bind_param("s", $username);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        
        if($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $user_id = $user_row['id'];
            
            // Insert main order
            $order_date = date('Y-m-d H:i:s');
            $status = 'Pending';
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, order_date) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $user_id, $total, $status, $order_date);
            $stmt->execute();
            $order_id = $conn->insert_id;
            
            // Insert order items
            foreach($items_array as $item){
                $name = $item['name'];
                $price = $item['price'];
                $quantity = $item['quantity'];

                $stmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, price, quantity) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("isii", $order_id, $name, $price, $quantity);
                $stmt->execute();
            }
            $msg = "Order Saved Successfully!";
        } else {
            $msg = "User not found!";
        }
    }
}

// Get menu items from database
$categories = [];
$cat_result = $conn->query("SELECT id, category_name FROM menu_categories ORDER BY id");
if($cat_result && $cat_result->num_rows > 0) {
    while($cat = $cat_result->fetch_assoc()) {
        $categories[$cat['id']] = $cat['category_name'];
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu | MCHUMI FOOD</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .header {
            background: white;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #333;
            font-size: 28px;
        }

        .user-info {
            color: #666;
            font-size: 14px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 30px;
        }

        .menu-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .menu-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #4facfe;
            padding-bottom: 10px;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }

        .item {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            background: white;
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
        }

        .item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .item-image {
            width: 100%;
            height: 150px;
            background: #ddd;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            loading: lazy;
            display: block;
        }

        .item-placeholder {
            font-size: 60px;
            color: #bbb;
        }

        .item-info {
            padding: 12px;
        }

        .item-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .item-price {
            color: #4facfe;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .item-btn {
            width: 100%;
            padding: 8px;
            background: #4facfe;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.2s;
        }

        .item-btn:hover {
            background: #00f2fe;
        }

        .sidebar {
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        .order-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .order-container h3 {
            color: #333;
            margin-bottom: 15px;
            border-bottom: 2px solid #4facfe;
            padding-bottom: 10px;
        }

        .order-list {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 15px;
        }

        .order-item {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 5px;
            border-left: 3px solid #4facfe;
            font-size: 13px;
        }

        .order-item-header {
            font-weight: bold;
            color: #333;
        }

        .order-item-details {
            color: #666;
            font-size: 12px;
            margin-top: 5px;
        }

        .total {
            background: #4facfe;
            color: white;
            padding: 15px;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: right;
        }

        .save-btn {
            width: 100%;
            padding: 12px;
            background: #22c55e;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.2s;
        }

        .save-btn:hover {
            background: #16a34a;
        }

        .empty-order {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
        }

        @media(max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }

            .menu {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }

            .sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>

<div class="header">
    <h1>🍽️ MCHUMI FOOD MENU</h1>
    <div class="user-info">
        Karibu: <strong><?= htmlspecialchars($username) ?></strong>
    </div>
</div>

<?php if($search !== ''): ?>
    <div style="max-width:1200px;margin:10px auto;padding:8px 20px;color:#333;">
        <strong>Umetafuta:</strong> <?= htmlspecialchars($search) ?>
    </div>
<?php endif; ?>

<div class="container">
    <div>
        <?php
        // Display menu by categories from database
        $categories_found = false;
        
        $cat_result = $conn->query("SELECT id, category_name FROM menu_categories ORDER BY id");
        if($cat_result && $cat_result->num_rows > 0):
            while($category = $cat_result->fetch_assoc()):
                $cat_id = $category['id'];
                $cat_name = $category['category_name'];
                
                $sql = "SELECT * FROM menu_items WHERE category_id = $cat_id " . $search_sql . " ORDER BY item_name";
                $result = $conn->query($sql);
                
                if($result && $result->num_rows > 0):
                    $categories_found = true;
        ?>
        <div class="menu-section">
            <h2>📦 <?= htmlspecialchars($cat_name) ?></h2>
            <div class="menu">
                <?php while($item = $result->fetch_assoc()): ?>
                <div class="item" data-name="<?= htmlspecialchars($item['item_name']) ?>" 
                     data-price="<?= htmlspecialchars($item['price']) ?>">
                    <div class="item-image">
                        <?php 
                        $has_image = false;
                        if(isset($item['image']) && !empty($item['image'])) {
                            $image_path = "user/images/" . $item['image'];
                            if(file_exists($image_path)) {
                                $has_image = true;
                        ?>
                            <img src="<?= htmlspecialchars($image_path) ?>" 
                                 alt="<?= htmlspecialchars($item['item_name']) ?>"
                                 loading="lazy"
                                 decoding="async">
                        <?php 
                            }
                        }
                        if(!$has_image) {
                        ?>
                            <div class="item-placeholder">🍽️</div>
                        <?php } ?>
                    </div>
                    <div class="item-info">
                        <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                        <div class="item-price"><?= number_format($item['price']) ?> Tsh</div>
                        <button class="item-btn" onclick="addToOrder(this)">Add</button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
        <?php 
                endif;
            endwhile;
        endif;
        
        if(!$categories_found) {
            echo '<div class="menu-section" style="text-align:center; padding:40px;">
                    <p style="color:#999; font-size:18px;">📭 No menu items available. Please contact admin to add items.</p>
                  </div>';
        }
        ?>
    </div>

    <div class="sidebar">
        <div class="order-container">
            <h3>📦 Order List</h3>
            <form method="POST">
                <div class="order-list" id="order-list">
                    <div class="empty-order">No items</div>
                </div>
                <div class="total" id="total">Total: 0 Tsh</div>
                <input type="hidden" name="items" id="items-json" value="[]">
                <input type="hidden" name="total" id="total-input" value="0">
                <button type="submit" name="save_order" class="save-btn">💾 SAVE ORDER</button>
            </form>
        </div>
    </div>
</div>

<script>
let order = [];

function addToOrder(btn){
    const itemDiv = btn.closest('.item');
    const name = itemDiv.getAttribute('data-name');
    const price = parseInt(itemDiv.getAttribute('data-price'));
    
    let found = order.find(i => i.name === name);
    if(found){
        found.quantity += 1;
    } else {
        order.push({name: name, price: price, quantity: 1});
    }
    renderOrder();
}

function renderOrder(){
    const list = document.getElementById('order-list');
    
    if(order.length === 0) {
        list.innerHTML = '<div class="empty-order">No items</div>';
        document.getElementById('total').innerText = 'Total: 0 Tsh';
        document.getElementById('total-input').value = '0';
        document.getElementById('items-json').value = '[]';
        return;
    }
    
    list.innerHTML = '';
    let total = 0;
    
    order.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        const div = document.createElement('div');
        div.className = 'order-item';
        div.innerHTML = `
            <div class="order-item-header">${item.name}</div>
            <div class="order-item-details">
                ${item.quantity} x ${formatNumber(item.price)} = ${formatNumber(subtotal)} Tsh
                <button type="button" style="margin-left:5px; padding:2px 6px; background:#ef4444; color:white; border:none; border-radius:3px; cursor:pointer; font-size:11px;" onclick="removeFromOrder(${index})">Remove</button>
            </div>
        `;
        list.appendChild(div);
        total += subtotal;
    });
    
    document.getElementById('total').innerText = 'Total: ' + formatNumber(total) + ' Tsh';
    document.getElementById('total-input').value = total;
    document.getElementById('items-json').value = JSON.stringify(order);
}

function removeFromOrder(index) {
    order.splice(index, 1);
    renderOrder();
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>

<?php if(isset($msg)): ?>
    <script>alert('<?= addslashes($msg) ?>');</script>
<?php endif; ?>

</body>
</html>
