<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Edit Menu Item</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
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
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #4facfe;
            box-shadow: 0 0 5px rgba(79, 172, 254, 0.3);
        }
        button {
            width: 100%;
            padding: 12px;
            background: #4facfe;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #00f2fe;
        }
        .preview {
            margin-top: 20px;
            text-align: center;
        }
        .preview img {
            max-width: 200px;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #4facfe;
            text-decoration: none;
        }
        .current-image {
            margin-top: 10px;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>✏️ Edit Menu Item</h2>

    <?php 
    include "db.php"; 
    include "auth.php";
    
    $id = $_GET['id'];
    $m = $conn->query("SELECT * FROM menu_items WHERE id=$id")->fetch_assoc();
    
    $msg = "";
    
    // Function to compress and optimize image
    function compressImage($source, $destination, $maxWidth = 500, $maxHeight = 500, $quality = 75) {
        $imageInfo = getimagesize($source);
        $width = $imageInfo[0];
        $height = $imageInfo[1];
        $type = $imageInfo[2];
        
        // Calculate new dimensions
        $ratio = min($maxWidth / $width, $maxHeight / $height);
        $newWidth = intval($width * $ratio);
        $newHeight = intval($height * $ratio);
        
        // Create image resources based on type
        switch($type) {
            case IMAGETYPE_JPEG:
                $source_img = imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $source_img = imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $source_img = imagecreatefromgif($source);
                break;
            default:
                return false;
        }
        
        // Create new image
        $target_img = imagecreatetruecolor($newWidth, $newHeight);
        
        // Handle PNG transparency
        if($type == IMAGETYPE_PNG) {
            imagealphablending($target_img, false);
            imagesavealpha($target_img, true);
            $transparent = imagecolorallocatealpha($target_img, 255, 255, 255, 127);
            imagefill($target_img, 0, 0, $transparent);
        }
        
        // Resize and copy
        imagecopyresampled($target_img, $source_img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save optimized image
        imagejpeg($target_img, $destination, $quality);
        
        imagedestroy($source_img);
        imagedestroy($target_img);
        
        return true;
    }
    
    if($_POST){
        $item = $_POST['item'];
        $price = $_POST['price'];
        $category = $_POST['category'];
        
        // Handle image upload
        if($_FILES['image']['name']){
            $target_dir = "../user/images/";
            if(!is_dir($target_dir)){
                mkdir($target_dir, 0777, true);
            }
            
            // Validate image file
            $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $validTypes = array('jpg', 'jpeg', 'png', 'gif');
            
            if(!in_array($imageFileType, $validTypes)) {
                $msg = "❌ Only JPG, PNG, and GIF files are allowed!";
            } else if($_FILES['image']['size'] > 5000000) { // 5MB max
                $msg = "❌ File is too large (max 5MB)!";
            } else {
                // Delete old image
                if(!empty($m['image']) && file_exists($target_dir . $m['image'])){
                    unlink($target_dir . $m['image']);
                }
                
                $image_name = time() . ".jpg"; // Always save as JPG for optimization
                $temp_file = $_FILES['image']['tmp_name'];
                $target_file = $target_dir . $image_name;
                
                // Compress image
                if(compressImage($temp_file, $target_file)) {
                    $conn->query("UPDATE menu_items SET 
                        item_name='$item',
                        price='$price',
                        category_id='$category',
                        image='$image_name'
                        WHERE id=$id");
                    
                    header("Location: menu.php");
                } else {
                    $msg = "❌ Error processing image!";
                }
            }
        } else {
            $conn->query("UPDATE menu_items SET 
                item_name='$item',
                price='$price',
                category_id='$category'
                WHERE id=$id");
            
            header("Location: menu.php");
        }
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Item Name</label>
            <input type="text" name="item" value="<?= htmlspecialchars($m['item_name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Price (Tsh)</label>
            <input type="number" name="price" value="<?= htmlspecialchars($m['price']) ?>" required>
        </div>

        <div class="form-group">
            <label>Category</label>
            <select name="category" required>
                <?php
                $cat_result = $conn->query("SELECT id, category_name FROM menu_categories ORDER BY id");
                if($cat_result && $cat_result->num_rows > 0) {
                    while($cat = $cat_result->fetch_assoc()) {
                        $selected = ($m['category_id'] == $cat['id']) ? 'selected' : '';
                        echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['category_name']) . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Image</label>
            <input type="file" name="image" accept="image/*">
            <?php if(!empty($m['image']) && file_exists("../menu_images/" . $m['image'])): ?>
                <div class="current-image">Current image: <strong><?= htmlspecialchars($m['image']) ?></strong></div>
            <?php endif; ?>
        </div>

        <button type="submit">Update Item</button>
    </form>

    <div class="back-link">
        <a href="menu.php">← Back to Menu</a>
    </div>
</div>

<script>
    document.querySelector('input[type="file"]').addEventListener('change', function(e){
        const file = e.target.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(event){
                let preview = document.querySelector('.preview');
                if(!preview){
                    preview = document.createElement('div');
                    preview.className = 'preview';
                    document.querySelector('form').appendChild(preview);
                }
                preview.innerHTML = '<img src="' + event.target.result + '" alt="Preview">';
            };
            reader.readAsDataURL(file);
        }
    });
</script>

</body>
</html>
