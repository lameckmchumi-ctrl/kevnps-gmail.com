<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Test Image Paths</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        .test { padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; }
        .ok { background: #d4edda; }
        .fail { background: #f8d7da; }
    </style>
</head>
<body>

<h1>Testing Image Paths & Database</h1>

<?php
include "db.php";

echo '<h2>1. Checking Files in user/images/:</h2>';
$dir = 'user/images/';
if(is_dir($dir)) {
    $files = scandir($dir);
    $files = array_diff($files, ['.', '..']);
    foreach($files as $f) {
        $path = $dir . $f;
        $exists = file_exists($path) ? '✅ EXISTS' : '❌ NOT FOUND';
        echo '<div class="test ok">' . htmlspecialchars($f) . ' - ' . $exists . '</div>';
    }
} else {
    echo '<div class="test fail">❌ Directory not found: ' . $dir . '</div>';
}

echo '<h2>2. Database menu_items table:</h2>';
$items = $conn->query("SELECT id, item_name, image FROM menu_items ORDER BY id");
while($item = $items->fetch_assoc()) {
    $img = $item['image'] ?? '(NULL)';
    $check_class = (file_exists('user/images/' . $item['image'])) ? 'ok' : 'fail';
    echo '<div class="test ' . $check_class . '">';
    echo 'ID: ' . $item['id'] . ' | ';
    echo 'Name: ' . htmlspecialchars($item['item_name']) . ' | ';
    echo 'Image: ' . htmlspecialchars($img);
    if(!empty($item['image'])) {
        if(file_exists('user/images/' . $item['image'])) {
            echo ' ✅ (<img src="user/images/' . htmlspecialchars($item['image']) . '" style="height:30px; width:30px; object-fit:cover; border-radius:3px;">)';
        } else {
            echo ' ❌ File missing';
        }
    }
    echo '</div>';
}

echo '<h2>3. Test Direct Image Display:</h2>';
$test_image = 'chai_ya_langi.jpg';
$test_path = 'user/images/' . $test_image;
echo '<div class="test ok">';
echo 'Testing: ' . $test_path . '<br>';
echo 'File exists: ' . (file_exists($test_path) ? 'YES ✅' : 'NO ❌') . '<br>';
echo 'Display:<br>';
echo '<img src="' . htmlspecialchars($test_path) . '" style="max-width: 200px; height: 200px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">';
echo '</div>';

echo '<h2>4. Auto-Link Now:</h2>';
echo '<p><a href="link_images_now.php" style="padding: 10px 20px; background: #4facfe; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">🔄 Click Here to Link All Images</a></p>';
?>

</body>
</html>
