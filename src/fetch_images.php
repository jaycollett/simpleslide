<?php
// Get environment variables
$pathToImages = "/var/www/html/images";

// Function to load image files from a directory
function getImageFiles($directory) {
    $supportedExtensions = ['png', 'jpg', 'jpeg', 'gif', 'bmp'];
    $imageFiles = [];
    if (is_dir($directory)) {
        $files = scandir($directory);
        foreach ($files as $file) {
            $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($fileExtension, $supportedExtensions)) {
                $imageFiles[] = '/images/' . basename($file);
            }
        }
    }
    return $imageFiles;
}

// Return the list of image files from the images directory, shuffled, as JSON
$imageFiles = getImageFiles($pathToImages);
shuffle($imageFiles);
header('Content-Type: application/json');
echo json_encode($imageFiles);
