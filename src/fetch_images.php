<?php
// PHP does not inherit the container's TZ environment variable; date() defaults to UTC even
// when the system clock is correct. Set it here too so any time-dependent logic in this
// endpoint agrees with index.php rather than silently running on UTC.
date_default_timezone_set(getenv('TZ') ?: 'UTC');

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
