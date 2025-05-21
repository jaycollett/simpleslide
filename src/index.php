<?php
// Get the delay value from the environment variable (default to 10 seconds if not set)
$slideDelayInSeconds = getenv('delayinsecs') ?: 10;

// Blackout logic
$blackoutEnabled = true;
$envBlackoutEnabled = getenv('BLACKOUT_ENABLED');
if ($envBlackoutEnabled !== false) {
    $blackoutEnabled = !in_array(strtolower($envBlackoutEnabled), ['0', 'false', 'off']);
}
$blackoutStart = getenv('BLACKOUT_START_HOUR') !== false ? (int)getenv('BLACKOUT_START_HOUR') : 22;
$blackoutStop = getenv('BLACKOUT_STOP_HOUR') !== false ? (int)getenv('BLACKOUT_STOP_HOUR') : 7;
$currentHour = (int)date('G');

if ($blackoutStart < $blackoutStop) {
    $inBlackout = ($currentHour >= $blackoutStart && $currentHour < $blackoutStop);
} else {
    $inBlackout = ($currentHour >= $blackoutStart || $currentHour < $blackoutStop);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Slide 0.1.0</title>
    <style>
        body {
            margin: 0;
            background-color: black;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }
        #slideshow-container {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        img {
            position: absolute;
            display: none;
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
        }
        img.active {
            display: block;
        }
    </style>
</head>
<body>
    <div id="slideshow-container"></div>

    <script>
        // Set blackout state from PHP
        const inBlackout = <?php echo $inBlackout ? 'true' : 'false'; ?>;
        let images = [];
        let currentIndex = 0;
        // Use the PHP value from the environment variable for the delay
        const slideDelayInSeconds = <?php echo $slideDelayInSeconds; ?>;
        const slideshowContainer = document.getElementById('slideshow-container');
        // Auto-reload the page every slideDelayInSeconds during blackout to catch transition
        if (inBlackout) {
            setInterval(() => { window.location.reload(); }, slideDelayInSeconds * 1000);
        }

        let currentIndex = 0;
        // Use the PHP value from the environment variable for the delay
        const slideDelayInSeconds = <?php echo $slideDelayInSeconds; ?>;
        const slideshowContainer = document.getElementById('slideshow-container');

        // Fetch the list of images from the server
        function fetchImages() {
            fetch('fetch_images.php')
                .then(response => response.json())
                .then(data => {
                    images = data.filter(img => img !== '/static/black.jpg');
                    currentIndex = 0;
                    shuffleImages();
                    updateSlideshow();
                })
                .catch(error => console.error('Error fetching images:', error));
        }

        // Shuffle the images randomly
        function shuffleImages() {
            for (let i = images.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [images[i], images[j]] = [images[j], images[i]]; // Swap elements
            }
        }

        function updateSlideshow() {
            // Clear existing images
            slideshowContainer.innerHTML = '';
            if (inBlackout) {
                // Show only black.jpg
                const img = document.createElement('img');
                img.src = '/static/black.jpg';
                img.alt = 'Blackout';
                img.classList.add('active');
                img.onload = () => resizeImage(img);
                slideshowContainer.appendChild(img);
            } else {
                // Add new images to the container
                images.forEach((src, index) => {
                    const img = document.createElement('img');
                    img.src = src;
                    img.alt = 'Slideshow Image';
                    img.loading = 'lazy';
                    if (index === 0) img.classList.add('active'); // Show the first image
                    img.onload = () => resizeImage(img); // Resize the image after it loads
                    slideshowContainer.appendChild(img);
                });
            }
            // Add a resize listener to ensure images adapt to screen changes
            window.addEventListener('resize', resizeAllImages);
        }

        function resizeImage(img) {
            const containerWidth = slideshowContainer.offsetWidth;
            const containerHeight = slideshowContainer.offsetHeight;

            if (img.naturalWidth / img.naturalHeight > containerWidth / containerHeight) {
                // Image is wider than the container
                img.style.width = '100%';
                img.style.height = 'auto';
            } else {
                // Image is taller than the container
                img.style.width = 'auto';
                img.style.height = '100%';
            }
        }

        function resizeAllImages() {
            const allImages = slideshowContainer.querySelectorAll('img');
            allImages.forEach(resizeImage);
        }

        function showNextImage() {
            if (inBlackout) {
                // Always show only black.jpg
                updateSlideshow();
                return;
            }
            const imageElements = slideshowContainer.querySelectorAll('img');
            if (imageElements.length === 0) return;
            imageElements[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % imageElements.length;
            imageElements[currentIndex].classList.add('active');
        }

        // Fetch images every hour
        setInterval(fetchImages, 3600 * 1000); // 1 hour interval

        // Start the slideshow with initial fetch
        fetchImages();
        setInterval(showNextImage, slideDelayInSeconds * 1000);
    </script>
</body>
</html>