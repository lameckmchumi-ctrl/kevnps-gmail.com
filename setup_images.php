<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Setup - Add Image Column</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; max-width: 600px; margin: 0 auto; border-radius: 8px; }
        h2 { color: #333; }
        .message { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        code { background: #f5f5f5; padding: 10px; display: block; margin: 10px 0; border-radius: 5px; }
        button { padding: 10px 20px; background: #4facfe; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>

<div class="container">
    <h2>🔧 Setup Database for Images</h2>

    <?php
    include "db.php";
    
    $output = '';
    
    // Step 1: Add image column if not exists
    $output .= '<h3>Step 1: Check & Add Image Column</h3>';
    
    $result = $conn->query("SHOW COLUMNS FROM menu_items LIKE 'image'");
    
    if($result && $result->num_rows > 0) {
        $output .= '<div class="message success">✅ Image column already exists</div>';
    } else {
        $output .= '<div class="message info">⏳ Adding image column...</div>';
        
        if($conn->query("ALTER TABLE menu_items ADD COLUMN image VARCHAR(255)")) {
            $output .= '<div class="message success">✅ Image column added successfully!</div>';
        } else {
            $output .= '<div class="message error">❌ Error: ' . $conn->error . '</div>';
        }
    }
    
    // Step 2: Link images automatically
    if(isset($_POST['auto_link'])) {
        $output .= '<h3>Step 2: Auto-Linking Images...</h3>';
        
        $images_dir = "user/images/";
        $image_files = scandir($images_dir);
        $image_files = array_diff($image_files, array('.', '..'));
        
        $updated = 0;
        $skipped = 0;
        
        foreach($image_files as $img) {
            $name_without_ext = strtolower(pathinfo($img, PATHINFO_FILENAME));
            $name_without_ext = str_replace('_', ' ', $name_without_ext);
            
            // Get all menu items
            $items = $conn->query("SELECT id, item_name FROM menu_items ORDER BY id");
            $found = false;
            
            while($item = $items->fetch_assoc()) {
                $item_name_lower = strtolower($item['item_name']);
                
                // Check if they match (flexible matching)
                if(strpos($item_name_lower, $name_without_ext) !== false || 
                   strpos($name_without_ext, $item_name_lower) !== false ||
                   levenshtein($item_name_lower, $name_without_ext) < 4) {
                    
                    $update_sql = "UPDATE menu_items SET image = '" . $conn->real_escape_string($img) . "' WHERE id = " . $item['id'];
                    if($conn->query($update_sql)) {
                        $output .= '<div class="message success">✅ ' . htmlspecialchars($item['item_name']) . ' ← ' . htmlspecialchars($img) . '</div>';
                        $updated++;
                        $found = true;
                        break;
                    }
                }
            }
            
            if(!$found) {
                $output .= '<div class="message info">⚠️ No match for: ' . htmlspecialchars($img) . '</div>';
                $skipped++;
            }
        }
        
        $output .= '<div class="message success"><strong>Done! Updated: ' . $updated . ', Skipped: ' . $skipped . '</strong></div>';
    }
    
    // Step 3: Show current status
    $output .= '<h3>Current Status:</h3>';
    
    $items = $conn->query("SELECT id, item_name, image FROM menu_items");
    $total = $items->num_rows;
    $with_image = $conn->query("SELECT COUNT(*) as cnt FROM menu_items WHERE image IS NOT NULL AND image != ''")->fetch_assoc();
    $with_image_count = $with_image['cnt'];
    
    $output .= '<div class="message info">';
    $output .= '<strong>Total Items:</strong> ' . $total . '<br>';
    $output .= '<strong>With Images:</strong> ' . $with_image_count . '<br>';
    $output .= '<strong>Missing Images:</strong> ' . ($total - $with_image_count);
    $output .= '</div>';
    
    echo $output;
    ?>

    <h3>Quick Actions:</h3>
    
    <form method="POST" style="margin-bottom: 20px;">
        <button type="submit" name="auto_link" onclick="return confirm('Auto-link all images with menu items?')">
            🔄 Auto-Link Images
        </button>
    </form>

    <p style="color: #666; font-size: 14px;">
        After setup, go to <a href="debug_images.php"><strong>debug_images.php</strong></a> to manually link any remaining images.
    </p>
</div>

</body>
</html>
