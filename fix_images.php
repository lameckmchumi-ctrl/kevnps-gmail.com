<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Fix Images - Direct Solution</title>
    <style>
        body { font-family: Arial; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h2, h3 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4facfe; color: white; }
        .btn { padding: 10px 20px; background: #4facfe; color: white; border: none; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn:hover { background: #00f2fe; }
        .code { background: #f5f5f5; padding: 15px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        form { margin: 20px 0; }
        select, input { padding: 8px; margin: 5px; border-radius: 5px; border: 1px solid #ddd; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 20px 0; }
        img { max-width: 100px; height: 100px; object-fit: cover; border-radius: 5px; }
    </style>
</head>
<body>

<div class="container">
    <h2>🖼️ Fix Images - Direct Solution</h2>

    <?php
    include "db.php";
    
    // Step 0: Add image column if missing
    $result = $conn->query("SHOW COLUMNS FROM menu_items LIKE 'image'");
    if(!$result || $result->num_rows == 0) {
        echo '<div style="background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0;">';
        echo '<strong>Adding image column to database...</strong><br>';
        
        if($conn->query("ALTER TABLE menu_items ADD COLUMN image VARCHAR(255)")) {
            echo '<span class="success">✅ Image column added!</span>';
        } else {
            echo '<span class="error">❌ Error adding column: ' . $conn->error . '</span>';
        }
        echo '</div>';
    }
    
    // Handle form submissions
    if(isset($_POST['action'])) {
        if($_POST['action'] == 'link') {
            $item_id = intval($_POST['item_id']);
            $image = $conn->real_escape_string($_POST['image']);
            
            if($conn->query("UPDATE menu_items SET image = '$image' WHERE id = $item_id")) {
                echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin: 20px 0; color: green;">';
                echo '✅ Linked successfully!';
                echo '</div>';
            } else {
                echo '<div style="background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin: 20px 0; color: red;">';
                echo '❌ Error: ' . $conn->error;
                echo '</div>';
            }
        }
        
        if($_POST['action'] == 'auto_link') {
            echo '<div style="background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;">';
            
            $mapping = [
                'Wali Nyama' => 'Wali_Nyama.jpg',
                'Wali Kuku' => 'Wali_Kuku.jpg',
                'Ugali Nyama' => 'Ugali_Nyama.jpg',
                'Pizza' => 'pizaa.jpg',
                'Bagga' => 'bagga.jpg',
                'Chips' => 'chips.jpg',
                'Chai ya Langi' => 'chai_ya_langi.jpg',
                'Chai ya Maziwa' => 'chai_ya_maziwa.jpg',
                'Maji' => 'maji.jpg',
                'Soda' => 'soda.jpg',
                'Juice' => 'juice.jpg'
            ];
            
            $count = 0;
            foreach($mapping as $item_name => $image_file) {
                $img = $conn->real_escape_string($image_file);
                if($conn->query("UPDATE menu_items SET image = '$img' WHERE item_name = '$item_name'")) {
                    echo '<span class="success">✅</span> ' . htmlspecialchars($item_name) . ' ← ' . htmlspecialchars($image_file) . '<br>';
                    $count++;
                } else {
                    echo '<span class="error">❌</span> ' . htmlspecialchars($item_name) . '<br>';
                }
            }
            
            echo '<br><strong>Total updated: ' . $count . '</strong>';
            echo '</div>';
        }
    }
    
    // Show all images in user/images folder
    echo '<h3>📁 Available Images in user/images/:</h3>';
    $images = scandir("user/images/");
    $images = array_diff($images, ['.', '..']);
    echo '<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; margin: 20px 0;">';
    foreach($images as $img) {
        echo '<div style="text-align: center; border: 1px solid #ddd; padding: 10px; border-radius: 5px;">';
        echo '<img src="user/images/' . htmlspecialchars($img) . '" alt="' . htmlspecialchars($img) . '" style="max-width: 100px; height: 100px; object-fit: cover;"><br>';
        echo '<small>' . htmlspecialchars($img) . '</small>';
        echo '</div>';
    }
    echo '</div>';
    
    // Show database items
    echo '<h3>📋 Menu Items in Database:</h3>';
    $items = $conn->query("SELECT id, item_name, image FROM menu_items ORDER BY id");
    echo '<table>';
    echo '<tr><th>ID</th><th>Item Name</th><th>Current Image</th><th>File Exists?</th><th>Preview</th></tr>';
    
    while($item = $items->fetch_assoc()) {
        $image_val = $item['image'] ?? '';
        $exists = '';
        $preview = '';
        
        if(!empty($image_val)) {
            if(file_exists("user/images/" . $image_val)) {
                $exists = '<span class="success">✅ Yes</span>';
                $preview = '<img src="user/images/' . htmlspecialchars($image_val) . '" alt="preview">';
            } else {
                $exists = '<span class="error">❌ No</span>';
            }
        } else {
            $exists = '<span class="error">❌ NULL</span>';
        }
        
        echo '<tr>';
        echo '<td>' . $item['id'] . '</td>';
        echo '<td>' . htmlspecialchars($item['item_name']) . '</td>';
        echo '<td>' . htmlspecialchars($image_val) . '</td>';
        echo '<td>' . $exists . '</td>';
        echo '<td>' . $preview . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    
    echo '<h3>⚡ Quick Auto-Link (Recommended):</h3>';
    echo '<form method="POST">';
    echo '<p>This will automatically link items to images based on names:</p>';
    echo '<button type="submit" name="action" value="auto_link" class="btn" onclick="return confirm(\'Auto-link all images?\')">🔄 Auto-Link All Images</button>';
    echo '</form>';
    
    echo '<h3>🔗 Manual Link (if needed):</h3>';
    echo '<form method="POST">';
    echo '<div class="row">';
    echo '<div>';
    echo '<label>Select Item:</label><br>';
    echo '<select name="item_id" required>';
    echo '<option value="">-- Choose Item --</option>';
    $items = $conn->query("SELECT id, item_name FROM menu_items ORDER BY item_name");
    while($item = $items->fetch_assoc()) {
        echo '<option value="' . $item['id'] . '">' . htmlspecialchars($item['item_name']) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    
    echo '<div>';
    echo '<label>Select Image:</label><br>';
    echo '<select name="image" required>';
    echo '<option value="">-- Choose Image --</option>';
    $images = scandir("user/images/");
    $images = array_diff($images, ['.', '..']);
    foreach($images as $img) {
        echo '<option value="' . htmlspecialchars($img) . '">' . htmlspecialchars($img) . '</option>';
    }
    echo '</select>';
    echo '</div>';
    echo '</div>';
    
    echo '<button type="submit" name="action" value="link" class="btn">Link Image</button>';
    echo '</form>';
    
    echo '<h3>✅ Test Display:</h3>';
    echo '<p>After linking, go to:</p>';
    echo '<p><a href="menu.php" style="color: #4facfe; text-decoration: none; font-weight: bold;">→ View Menu (menu.php)</a></p>';
    ?>

</div>

</body>
</html>
