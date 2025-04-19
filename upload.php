<!-- This page shows an HTML form to upload an image. -->
<!-- it handles the submitted image by 
  writing it to disk in a folder called /images/{booth-name} where {booth-name} is a 
  url parameter of the current page (e.g. /upload.php?booth_name=mybooth) -->
<!-- After upload, the user is redirected to /view.php?booth_name=mybooth -->
<?php

const MAX_SUBMISSIONS_PER_DAY = 3;
const SUBMISSION_EXPIRATION_TIME = 86400; // 1 day in seconds

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

// Get their submissions from the cookie
if (isset($_COOKIE['booth_submissions'])) {
    $submissions = json_decode($_COOKIE['booth_submissions'], true);
} else {
    $submissions = [];
}

// Filter to keep only this booth's submissions
$booth_submissions = array_filter($submissions, function ($submission) use ($booth_name) {
    return $submission['booth_name'] === $booth_name;
});

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check number of submissions so far
    if (count($booth_submissions) >= MAX_SUBMISSIONS_PER_DAY) {
        header("Location: /error.php?error=too_many_submissions");
        exit();
    }

    // Check if the booth name is a valid directory name and if not, create it
    $booth_dir = __DIR__ . '/images/' . $booth_name;
    if (!is_dir($booth_dir)) {
        if (!mkdir($booth_dir, 0777, true)) {
            header("Location: /error.php?error=fail_make_booth_name_folder");
            exit();
        }
    }

    // Check if the file is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $file_name = basename($file['name']);
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

        // Check if the file is an image
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_type, $allowed_types)) {
            // Check if the file size is less than 5MB
            if ($file_size <= 5 * 1024 * 1024) {
                // Move the uploaded file to the booth directory
                // Their filename is in the format 2025-04-19T22-03-13-ead453.jpg
                $new_file_name = date('Y-m-d\TH-i-s') . '-' . uniqid() . '.' . $file_type;
                $destination = $booth_dir . '/' . $new_file_name;
                if (move_uploaded_file($file_tmp, $destination)) {

                    // Update their cookie with a new submission
                    // Add the new submission
                    $submissions[] = [
                        'booth_name' => $booth_name,
                        'file_name' => $new_file_name,
                        'timestamp' => time()
                    ];
                    // Save the updated submissions to the cookie
                    // When cookie expires, they can upload again
                    setcookie('booth_submissions', json_encode($submissions), time() + SUBMISSION_EXPIRATION_TIME, "/");

                    header("Location: /view.php?booth_name=" . urlencode($booth_name));
                    exit();
                } else {
                    header("Location: /error.php?error=fail_move_uploaded_file");
                    exit();
                }
            } else {
                header("Location: /error.php?error=file_too_large");
                exit();
            }
        } else {
            header("Location: /error.php?error=invalid_file_type");
            exit();
        }
    } else {
        header("Location: /error.php?error=file_upload_error");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Photobooth</title>
    <link rel="preload" hryygtef="/ABCRepro-Medium-Trial.woff" as="font" type="font/woff" crossorigin="anonymous">
    <style>
        @font-face {
            font-family: 'ABCRepro';
            src: url('/ABCRepro-Medium-Trial.woff') format('woff');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'ABCRepro', sans-serif;
            background-color: black;
            color: white;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: #333333;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        input[type="file"] {
            display: block;
            margin: 20px auto;
        }

        button {
            display: block;
            margin: auto;
        }

        a:visited {
            color: #00DDFF
        }
        a {
            color: #00DDFF
        }
    </style>

</head>

<body>
    <div class="container">
        <h1>Virtual Photobooth</h1>
        <p>You found the photobooth! Upload a selfie here, to add to the collection of
            selfie images taken by other ravers.</p>
        <div style="width: 100%; text-align: center;">
            <img src="/book.gif" style="height: 30vh; width: auto;margin: 0 auto;" alt="wtf">
        </div>
        <p>You only get 3 pictures max per booth per day!</p>
        <p style="color: red">You have <?php echo MAX_SUBMISSIONS_PER_DAY - count($booth_submissions); ?> pictures left today in booth <?php echo $booth_name ?>.</p>
        <form action="" method="POST" enctype="multipart/form-data">
            <input style="color: white; background-color: #444444; border: none; padding: 10px; border-radius: 5px;"
                type="file" name="image" accept="image/*" required>
            <button type="submit" style="background-color: #007BFF; color: white; padding: 10px 20px; border: none; border-radius: 5px;">
                Upload</button>
        </form>
        <a href="/view.php?booth_name=<?php echo urlencode($booth_name); ?>">View Images</a>
    </div>
    <footer>
        <p>Made with no fucks given</p>
    </footer>
</body>

</html>