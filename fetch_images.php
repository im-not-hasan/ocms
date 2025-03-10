<?php
$brand = $_GET['brand'];
$material = $_GET['material'];

$dir = 'Eyewear/' . $brand . '/' . $material . '/'; 
$images = glob($dir . '*.png'); 

$imageData = [];
foreach ($images as $image) {
  $imageData[] = [
    'src' => $image,
    'name' => basename($image, '.png')
  ];
}

echo json_encode($imageData);
?>
