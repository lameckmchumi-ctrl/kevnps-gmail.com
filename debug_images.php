<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Debug Images</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; max-width: 1000px; margin: 0 auto; border-radius: 8px; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4facfe; color: white; }
        .error { color: red; }
        .success { color: green; }
        .warning { color: orange; }
        img { max-width: 100px; height: 100px; object-fit: cover; }
    </style>
</head>
<body>

<div class="container">
    <h2>🔍 Debug: Images & Database</h2>

    <?php
    include "db.php";
    
    echo '<h3>1️⃣ Checking Database Structure...</h3>';
    
    $result = $conn->query("DESCRIBE menu_items");
    echo '<table><tr><th>Field</th><th>Type</th><th>Status</th></tr>';
    
    $has_image_column = false;
    while($row = $result->fetch_assoc()) {
        $status = '';
        if($row['Field'] == 'image') {
            $has_image_column = true;
            $status = '<span class="success">✅ Image column exists</span>';
        }
        echo '<tr><td>' . $row['Field'] . '</td><td>' . $row['Type'] . '</td><td>' . $status . '</td></tr>';
    }
    echo '</table>';
    
    if(!$has_image_column) {
        echo '<p class="error">❌ Image column NOT found! Need to add it.</p>';
        echo '<p>Run this SQL:</p>';
        echo '<pre>ALTER TABLE menu_items ADD COLUMN image VARCHAR(255);</pre>';
    }
    
    echo '<h3>2️⃣ Checking Image Files...</h3>';
    
    $image_dir = "user/images/";
    if(is_dir($image_dir)) {
        $files = scandir($image_dir);
        $files = array_diff($files, array('.', '..'));
        echo '<p class="success">✅ Found ' . count($files) . ' images:</p>';
        echo '<ul>';
        foreach($files as $file) {
            echo '<li>' . htmlspecialchars($file) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p class="error">❌ Directory not found: ' . $image_dir . '</p>';
    }
    
    echo '<h3>3️⃣ Menu Items & Images in Database...</h3>';
    
    $items = $conn->query("SELECT id, item_name, image FROM menu_items");
    echo '<table>';
    echo '<tr><th>ID</th><th>Item Name</th><th>Image Field</th><th>File Exists?</th><th>Preview</th></tr>';
    
    while($item = $items->fetch_assoc()) {
        $image = $item['image'] ?? 'NULL';
        $file_exists = 'N/A';
        $preview = 'N/A';
        
        if(!empty($image)) {
            $img_path = "user/images/" . $image;
            if(file_exists($img_path)) {
                $file_exists = '<span class="success">✅ Yes</span>';
                $preview = '<img src="' . htmlspecialchars($img_path) . '" alt="preview">';
            } else {
                $file_exists = '<span class="error">❌ No - ' . $img_path . '</span>';
            }
        } else {
            $file_exists = '<span class="warning">⚠️ NULL/Empty</span>';
        }
        
        echo '<tr>
            <td>' . $item['id'] . '</td>
            <td>' . htmlspecialchars($item['item_name']) . '</td>
            <td>' . htmlspecialchars($image) . '</td>
            <td>' . $file_exists . '</td>
            <td>' . $preview . '</td>
        </tr>';
    }
    echo '</table>';
    
    echo '<h3>4️⃣ Manual Image Linking...</h3>';
    
    if(isset($_POST['link_image'])) {
        $item_id = $_POST['item_id'];
        $image_file = $_POST['image_file'];
        
        $update_sql = "UPDATE menu_items SET image = '" . $conn->real_escape_string($image_file) . "' WHERE id = " . intval($item_id);
        
        if($conn->query($update_sql)) {
            echo '<p class="success">✅ Updated item #' . $item_id . ' with image: ' . htmlspecialchars($image_file) . '</p>';
        } else {
            echo '<p class="error">❌ Error: ' . $conn->error . '</p>';
        }
    }
    
    echo '<h3>Quick Link Images Form:</h3>';
    echo '<form method="POST">';
    echo '<select name="item_id" required>';
    $items = $conn->query("SELECT id, item_name FROM menu_items");
    while($item = $items->fetch_assoc()) {
        echo '<option value="' . $item['id'] . '">' . htmlspecialchars($item['item_name']) . '</option>';
    }
    echo '</select> → ';
    
    echo '<select name="image_file" required>';
    $files = scandir("user/images/");
    $files = array_diff($files, array('.', '..'));
    foreach($files as $file) {
        echo '<option value="' . htmlspecialchars($file) . '">' . htmlspecialchars($file) . '</option>';
    }
    echo '</select>';
    
    echo '<button type="submit" name="link_image">Link Image</button>';
    echo '</form>';
    ?>

</div>

</body>
</html>
