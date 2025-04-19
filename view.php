<!-- displays every photo in the directory images/booth_name
 where booth_name is from the url param /view.php?booth_name=booth_name -->

 <!-- Theres a back button to /upload.php?booth_name=booth_name -->

<?php
// Check if the booth name is set in the URL
if (isset($_GET['booth_name'])) {
    $booth_name = $_GET['booth_name'];
} else {
    header("Location: /error.php?error=booth_name_undefined");
    exit();
}
// Check if the booth name is valid
if (!preg_match('/^[a-zA-Z0-9_-]{1,200}$/', $booth_name) || strlen($booth_name) > 200) {
    header("Location: /error.php?error=booth_name_invalid");
    exit();
}

// Check if a folder exists for that booth name
$booth_dir = __DIR__ . '/images/' . $booth_name;
if (!is_dir($booth_dir)) {
    header("Location: /error.php?error=booth_name_folder_does_not_exist");
    exit();
}

// Get all the images in the booth directory
$images = glob($booth_dir . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE);

// Keep only the last 10 images sorted by date
$images = array_slice(array_reverse($images), 0, 10);
// Sort the images by date
usort($images, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

?>

<!-- Black dark cool bg color -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booth View</title>
    <style>
        body {
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            text-align: center;
            min-height: 100vh;
        }
        h1 {
            margin-top: 20px;
        }
        .image-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }
        .image-container img {
            margin: 30px;
            border-radius: 10px;
        }
        .back-button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007BFF;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Booth: <?php echo htmlspecialchars($booth_name); ?></h1>
    <p>Note: we can't be held responsible for inappropriate pictures uploaded by users. We'll clean them up as they appear, but please allow 24hrs after a request has been made.</p>
    <div class="image-container">
        <?php if (empty($images)): ?>
            <p>No images found.</p>
        <?php else: ?>
            <?php foreach ($images as $image): ?>
                <h2> Uploaded on: <?php echo date("Y-m-d H:i:s", filemtime($image)); ?></h2>
                <img src="<?php echo htmlspecialchars(str_replace(__DIR__, '', $image)); ?>" style="width: 95vw; height: auto;" alt="Image">
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <footer>
        <p>Made with no fucks given</p>
    </footer>
</body>
