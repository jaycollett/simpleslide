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

// Get current hour (0-23)
$currentHour = (int)date('G');

// Get blackout enabled flag (default: enabled)
$blackoutEnabled = true;
$envBlackoutEnabled = getenv('BLACKOUT_ENABLED');
if ($envBlackoutEnabled !== false) {
    $blackoutEnabled = !in_array(strtolower($envBlackoutEnabled), ['0', 'false', 'off']);
}

// Get blackout window from environment variables (default: 22 to 7)
$blackoutStart = getenv('BLACKOUT_START_HOUR') !== false ? (int)getenv('BLACKOUT_START_HOUR') : 22;
$blackoutStop = getenv('BLACKOUT_STOP_HOUR') !== false ? (int)getenv('BLACKOUT_STOP_HOUR') : 7;

// Determine if current hour is in blackout window
$inBlackout = false;
if ($blackoutStart < $blackoutStop) {
    // e.g., 1 to 7
    $inBlackout = ($currentHour >= $blackoutStart && $currentHour < $blackoutStop);
} else {
    // e.g., 22 to 7 (overnight)
    $inBlackout = ($currentHour >= $blackoutStart || $currentHour < $blackoutStop);
}

// If blackout is enabled and in blackout window, return only the black image
if ($blackoutEnabled && $inBlackout) {
    $imageFiles = ['/static/black.jpg'];
} else {
    // Normal behavior
    $imageFiles = getImageFiles($pathToImages);
    shuffle($imageFiles);
}

// Return the image paths as JSON
header('Content-Type: application/json');
echo json_encode($imageFiles);
