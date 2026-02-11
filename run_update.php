<?php
// Temporary script to run the image UPDATE. Remove after use.
include __DIR__ . '/db.php';

$sql = "UPDATE menu_items SET image = CASE id
  WHEN 1 THEN 'chai_ya_maziwa.jpg'
  WHEN 2 THEN 'chai_ya_langi.jpg'
  WHEN 3 THEN 'soda.jpg'
  WHEN 4 THEN 'juice.jpg'
  WHEN 5 THEN 'bagga.jpg'
  WHEN 6 THEN 'chips.jpg'
  WHEN 7 THEN 'Wali_Kuku.jpg'
  WHEN 8 THEN 'Wali_Nyama.jpg'
  WHEN 9 THEN 'pizaa.jpg'
  WHEN 10 THEN 'Ugali_Nyama.jpg'
  WHEN 11 THEN 'maji.jpg'
  ELSE image
END;";

if($conn->query($sql) === TRUE) {
    echo "Update successful.\n";
    echo "Refresh <a href=\"/mchumi_foods/menu.php\">menu.php</a> to see images.";
} else {
    echo "Error running update: " . $conn->error;
}

// Suggest removing file after use for safety
echo "\n\nPlease delete this file (run_update.php) after confirming the update.";

?>
