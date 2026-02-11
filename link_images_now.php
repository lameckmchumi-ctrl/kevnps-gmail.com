<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Auto Link Images NOW</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .success { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0; }
    </style>
</head>
<body>

<h1>Linking Images to Menu Items...</h1>

<?php
include "db.php";

// First, add image column if missing
$result = $conn->query("SHOW COLUMNS FROM menu_items LIKE 'image'");
if(!$result || $result->num_rows == 0) {
    echo '<div class="success">Adding image column...</div>';
    $conn->query("ALTER TABLE menu_items ADD COLUMN image VARCHAR(255)");
}

// Direct mapping - EXACT MATCH with database items
$mapping = [
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
$notfound = [];

foreach($mapping as $item_name => $image_file) {
    // Find the exact item name in database
    $check = $conn->query("SELECT id FROM menu_items WHERE item_name = '" . $conn->real_escape_string($item_name) . "'");
    
    if($check && $check->num_rows > 0) {
        $row = $check->fetch_assoc();
        $update = $conn->query("UPDATE menu_items SET image = '" . $conn->real_escape_string($image_file) . "' WHERE id = " . $row['id']);
        
        if($update) {
            echo '<div class="success">✅ ' . htmlspecialchars($item_name) . ' → ' . htmlspecialchars($image_file) . '</div>';
            $updated++;
        }
    } else {
        $notfound[] = $item_name;
    }
}

echo '<h2>Result: ' . $updated . ' items updated</h2>';

if(count($notfound) > 0) {
    echo '<div class="error"><strong>Items not found in database:</strong>';
    foreach($notfound as $item) {
        echo '<br>❌ ' . htmlspecialchars($item);
    }
    echo '</div>';
}

// Show what's in database now
echo '<h3>Current Database Status:</h3>';
$items = $conn->query("SELECT id, item_name, image FROM menu_items ORDER BY id");
echo '<table border="1" style="border-collapse: collapse; width: 100%;"><tr><th>ID</th><th>Item Name</th><th>Image</th><th>File OK?</th></tr>';

while($item = $items->fetch_assoc()) {
    $img = $item['image'] ?? 'NULL';
    $check = (file_exists('user/images/' . $img)) ? '✅' : '❌';
    echo '<tr><td>' . $item['id'] . '</td><td>' . htmlspecialchars($item['item_name']) . '</td><td>' . htmlspecialchars($img) . '</td><td>' . $check . '</td></tr>';
}
echo '</table>';

echo '<hr>';
echo '<h3>Next:</h3>';
echo '<p><strong><a href="menu.php">→ Go to Menu to see images</a></strong></p>';
echo '<p>(or refresh menu.php if already open)</p>';
?>

</body>
</html>
