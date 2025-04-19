<?php
// error.php
// Check if the error message is set in the URL
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
} else {
    $error_message = 'Unknown error';
}
?>
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
            max-width: 200px;
            max-height: 200px;
            margin: 10px;
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
    <h1>Error</h1>
    <h3>Reason: <?php echo htmlspecialchars($error_message); ?></h3>
    <footer>
        <p>Made with no fucks given</p>
    </footer>
</body>

