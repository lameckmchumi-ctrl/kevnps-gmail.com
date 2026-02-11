<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Sync Images to Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            text-align: center;
        }
        .message {
            padding: 15px;
            margin: 15px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #4facfe;
            color: white;
        }
        .btn {
            padding: 10px 20px;
            background: #4facfe;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #00f2fe;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>🖼️ Sync Images to Menu Items</h2>

    <?php 
    include "db.php"; 
    include "auth.php";
    
    $images_dir = "../user/images/";
    $updated_count = 0;
    $matched_count = 0;
    
    // Get all image files
    $image_files = scandir($images_dir);
    $image_files = array_diff($image_files, array('.', '..'));
    
    echo '<div class="message info">';
    echo '<strong>Found ' . count($image_files) . ' images in user/images folder:</strong><br>';
    foreach($image_files as $img) {
        echo '- ' . htmlspecialchars($img) . '<br>';
    }
    echo '</div>';
    
    if(isset($_POST['sync'])) {
        echo '<div class="message success">';
        echo '<strong>Updating menu items with images...</strong><br><br>';
        
        foreach($image_files as $image_file) {
            $name_without_ext = pathinfo($image_file, PATHINFO_FILENAME);
            
            // Try to find matching item in database
            $sql = "SELECT id, item_name FROM menu_items ORDER BY id";
            $result = $conn->query($sql);
            
            // Simple matching: find item that sounds similar
            $found = false;
            while($item = $result->fetch_assoc()) {
                $item_name_lower = strtolower(str_replace(' ', '_', $item['item_name']));
                $image_name_lower = strtolower($name_without_ext);
                
                // Check if they match
                if(strpos($image_name_lower, $item_name_lower) !== false || 
                   strpos($item_name_lower, $image_name_lower) !== false) {
                    
                    // Update the item with image
                    $update_sql = "UPDATE menu_items SET image = '" . $conn->real_escape_string($image_file) . "' WHERE id = " . $item['id'];
                    if($conn->query($update_sql)) {
                        echo '✅ ' . htmlspecialchars($item['item_name']) . ' → ' . htmlspecialchars($image_file) . '<br>';
                        $updated_count++;
                        $found = true;
                        break;
                    }
                }
            }
            
            if(!$found) {
                echo '⚠️ No matching item for: ' . htmlspecialchars($image_file) . '<br>';
            }
        }
        
        echo '<br><strong>Total updated: ' . $updated_count . ' items</strong>';
        echo '</div>';
    }
    ?>

    <div class="message info">
        <strong>Instructions:</strong>
        <p>Click the button below to automatically match images with menu items.</p>
        <p>Images will be linked based on similar names.</p>
    </div>

    <form method="POST" style="text-align: center; margin: 30px 0;">
        <button type="submit" name="sync" class="btn" onclick="return confirm('Auto-sync images with menu items?')">
            🔄 Sync Images Now
        </button>
    </form>

    <h3>Current Menu Items:</h3>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Current Image</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $items = $conn->query("SELECT id, item_name, image FROM menu_items ORDER BY id");
            while($item = $items->fetch_assoc()) {
                $status = (isset($item['image']) && !empty($item['image'])) ? 
                    '<span style="color:green;">✅ Has image</span>' : 
                    '<span style="color:orange;">⚠️ No image</span>';
                echo '<tr>
                    <td>' . htmlspecialchars($item['item_name']) . '</td>
                    <td>' . htmlspecialchars($item['image'] ?? 'N/A') . '</td>
                    <td>' . $status . '</td>
                </tr>';
            }
            ?>
        </tbody>
    </table>

    <div style="text-align: center; margin-top: 30px;">
        <a href="menu.php" class="btn">← Back to Menu</a>
    </div>
</div>

</body>
</html>
