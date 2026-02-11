<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Full Diagnostic Report</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        h2 { color: #333; border-bottom: 2px solid #4facfe; padding-bottom: 10px; }
        .report { margin: 20px 0; padding: 15px; border-radius: 5px; border-left: 4px solid #4facfe; }
        .ok { background: #d4edda; border-left-color: #28a745; }
        .fail { background: #f8d7da; border-left-color: #dc3545; }
        .info { background: #d1ecf1; border-left-color: #17a2b8; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4facfe; color: white; }
        code { background: #f5f5f5; padding: 2px 5px; border-radius: 3px; }
        .copy-btn { padding: 5px 10px; background: #4facfe; color: white; border: none; border-radius: 3px; cursor: pointer; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <h1>🔍 Full Diagnostic Report</h1>
    <p><em>Last checked: <?php echo date('Y-m-d H:i:s'); ?></em></p>

    <?php
    include "db.php";
    
    echo '<h2>1️⃣ FILE SYSTEM CHECK</h2>';
    
    // Check directories
    $checks = [
        'user/images/' => 'Image folder',
        'user/images/chai_ya_langi.jpg' => 'Sample image (chai)',
        'user/images/Wali_Nyama.jpg' => 'Sample image (wali)'
    ];
    
    foreach($checks as $path => $desc) {
        $exists = file_exists($path);
        $class = $exists ? 'ok' : 'fail';
        $status = $exists ? '✅ EXISTS' : '❌ MISSING';
        echo '<div class="report ' . $class . '">';
        echo '<strong>' . $desc . '</strong><br>';
        echo '<code>' . $path . '</code> - ' . $status;
        echo '</div>';
    }
    
    // List all files in user/images
    echo '<h2>2️⃣ FILES IN user/images/ FOLDER</h2>';
    $image_files = scandir('user/images/');
    $image_files = array_diff($image_files, ['.', '..']);
    
    if(count($image_files) > 0) {
        echo '<div class="report ok">';
        echo '<strong>Found ' . count($image_files) . ' files:</strong><br>';
        foreach($image_files as $f) {
            $size = filesize('user/images/' . $f);
            echo '• ' . htmlspecialchars($f) . ' (' . round($size/1024, 2) . ' KB)<br>';
        }
        echo '</div>';
    } else {
        echo '<div class="report fail"><strong>❌ No files found!</strong></div>';
    }
    
    echo '<h2>3️⃣ DATABASE CHECK</h2>';
    
    // Check if image column exists
    $result = $conn->query("SHOW COLUMNS FROM menu_items LIKE 'image'");
    if($result && $result->num_rows > 0) {
        echo '<div class="report ok"><strong>✅ image column exists in database</strong></div>';
    } else {
        echo '<div class="report fail"><strong>❌ image column MISSING!</strong><br>';
        echo 'Need to run: <code>ALTER TABLE menu_items ADD COLUMN image VARCHAR(255);</code></div>';
    }
    
    echo '<h2>4️⃣ MENU ITEMS & IMAGE LINKS</h2>';
    
    $items = $conn->query("SELECT id, item_name, image FROM menu_items ORDER BY category_id, item_name");
    
    echo '<table>';
    echo '<tr><th>ID</th><th>Item Name</th><th>Image Field</th><th>File Exists?</th><th>Test Display</th></tr>';
    
    $missing_images = 0;
    while($item = $items->fetch_assoc()) {
        $image = $item['image'] ?? '';
        $file_ok = false;
        $display = '—';
        
        if(!empty($image)) {
            $img_path = 'user/images/' . $image;
            if(file_exists($img_path)) {
                $file_ok = true;
                $display = '<img src="' . htmlspecialchars($img_path) . '" style="height:40px; width:40px; object-fit:cover; border-radius:3px;">';
            } else {
                $missing_images++;
            }
        } else {
            $missing_images++;
        }
        
        $status = $file_ok ? '✅' : '❌';
        
        echo '<tr>';
        echo '<td>' . $item['id'] . '</td>';
        echo '<td>' . htmlspecialchars($item['item_name']) . '</td>';
        echo '<td><code>' . htmlspecialchars($image ?: '(null)') . '</code></td>';
        echo '<td>' . $status . '</td>';
        echo '<td>' . $display . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    
    if($missing_images > 0) {
        echo '<div class="report fail"><strong>⚠️ ' . $missing_images . ' items missing images!</strong></div>';
    } else {
        echo '<div class="report ok"><strong>✅ All items have images linked!</strong></div>';
    }
    
    echo '<h2>5️⃣ MENU.PHP CODE CHECK</h2>';
    
    // Check if menu.php exists and what path it uses
    $menu_content = file_get_contents('menu.php');
    
    if(strpos($menu_content, "user/images/") !== false) {
        echo '<div class="report ok"><strong>✅ menu.php uses correct path: user/images/</strong></div>';
    } else {
        echo '<div class="report fail"><strong>❌ menu.php might use wrong path</strong></div>';
    }
    
    // Check for PHP errors in menu.php display code
    if(strpos($menu_content, 'file_exists') !== false) {
        echo '<div class="report info"><strong>ℹ️ menu.php has file_exists checks</strong></div>';
    }
    
    echo '<h2>6️⃣ QUICK ACTIONS</h2>';
    
    echo '<div style="margin: 20px 0;">';
    echo '<form method="POST" style="display: inline;">';
    echo '<button type="submit" name="action" value="add_column" style="padding: 10px 20px; background: #4facfe; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">';
    echo '➕ Add Image Column (if missing)';
    echo '</button>';
    echo '</form>';
    
    echo ' ';
    
    echo '<form method="POST" style="display: inline;">';
    echo '<button type="submit" name="action" value="link_images" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">';
    echo '🔗 Link All Images Now';
    echo '</button>';
    echo '</form>';
    echo '</div>';
    
    // Handle actions
    if(isset($_POST['action'])) {
        echo '<h2>Processing...</h2>';
        
        if($_POST['action'] == 'add_column') {
            echo '<div class="report info">Adding image column...</div>';
            if($conn->query("ALTER TABLE menu_items ADD COLUMN image VARCHAR(255)")) {
                echo '<div class="report ok">✅ Column added successfully!</div>';
            } else {
                if(strpos($conn->error, 'Duplicate column') !== false) {
                    echo '<div class="report info">Column already exists</div>';
                } else {
                    echo '<div class="report fail">Error: ' . htmlspecialchars($conn->error) . '</div>';
                }
            }
        }
        
        if($_POST['action'] == 'link_images') {
            echo '<div class="report info">Linking images...</div>';
            
            $mappings = [
                'chai ya langi' => 'chai_ya_langi.jpg',
                'chai ya maziwa' => 'chai_ya_maziwa.jpg',
                'Juice' => 'juice.jpg',
                'Maji' => 'maji.jpg',
                'Soda' => 'soda.jpg',
                'Bagga' => 'bagga.jpg',
                'Chips' => 'chips.jpg',
                'pizaa' => 'pizaa.jpg',
                'Ugali_Nyama' => 'Ugali_Nyama.jpg',
                'Wali Kuku' => 'Wali_Kuku.jpg',
                'Wali Nyama' => 'Wali_Nyama.jpg',
            ];
            
            $updated = 0;
            $already = 0;
            $notfound = [];
            
            foreach($mappings as $name => $image) {
                $check = $conn->query("SELECT id, image FROM menu_items WHERE item_name = '" . $conn->real_escape_string($name) . "'");
                if($check && $check->num_rows > 0) {
                    $row = $check->fetch_assoc();
                    if(empty($row['image'])) {
                        $conn->query("UPDATE menu_items SET image = '" . $conn->real_escape_string($image) . "' WHERE item_name = '" . $conn->real_escape_string($name) . "'");
                        echo 'Updated: ' . htmlspecialchars($name) . ' → ' . htmlspecialchars($image) . '<br>';
                        $updated++;
                    } else {
                        $already++;
                    }
                } else {
                    $notfound[] = $name;
                }
            }
            
            echo '<div class="report ok">';
            echo '<strong>✅ Updated: ' . $updated . ' | Already linked: ' . $already . '</strong>';
            if(count($notfound) > 0) {
                echo '<br><strong>⚠️ Not found in database:</strong> ' . htmlspecialchars(implode(', ', $notfound));
            }
            echo '</div>';
        }
        
        echo '<p><a href="test_images.php" style="color: #4facfe; text-decoration: none;">↻ Refresh this page</a></p>';
    }
    
    echo '<h2>7️⃣ FINAL TEST</h2>';
    echo '<div class="report info">';
    echo '<strong>Go to:</strong><br>';
    echo '<a href="menu.php" style="padding: 10px 20px; background: #4facfe; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin: 10px 0;">👉 View Menu (menu.php)</a><br><br>';
    echo '<strong>Picha zionekane?</strong> Share this report with me if still not working.';
    echo '</div>';
    ?>

</div>

</body>
</html>
