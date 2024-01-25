<?php
session_start();

if (!isset($_SESSION['EMAIL_USER']) || empty($_SESSION['PASSWORD_USER'])) {
    header("Location: login.php");
    exit();
}

// Create a PDO connection to the database
$dsn = 'mysql:host=localhost;dbname=main';
$username = 'root';
$password = '';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $userEmail = $_SESSION['EMAIL_USER'];

    // Retrieve user information from the database
    $sql = "SELECT * FROM registered_user WHERE EMAIL_USER = :EMAIL_USER";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':EMAIL_USER', $userEmail);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $ROLE = $result['role'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle profile picture update
        if (isset($_FILES["profiles_picture"]) && $_FILES["profiles_picture"]["error"] == 0) {
            $targetDir = './profiles_pictures/'; // Specify the directory where you want to store the profile images
            $targetFile = $targetDir . basename($_FILES["profiles_picture"]["name"]);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check if the uploaded file is an image
            $check = getimagesize($_FILES["profiles_picture"]["tmp_name"]);
            if ($check !== false) {
                // Generate a unique filename to prevent overwriting existing files
                $newFilename = uniqid() . '.' . $imageFileType;
                $newFilePath = $targetDir . $newFilename;

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES["profiles_picture"]["tmp_name"], $newFilePath)) {
                    // Update the profile picture in the database
                    $sql = "UPDATE registered_user SET profile_picture = :profile_picture WHERE EMAIL_USER = :EMAIL_USER";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':profile_picture', $newFilePath);
                    $stmt->bindParam(':EMAIL_USER', $userEmail);
                    $stmt->execute();

                    // Update the session variable with the new profile picture
                    $_SESSION['PROFILE_PICTURE'] = $newFilePath;

                    // Redirect to the profile page
                    header("Location: profile.php");
                    exit();
                } else {
                    echo "Failed to move the uploaded file.";
                }
            } else {
                echo "The uploaded file is not a valid image.";
            }
        }
    }
} catch (PDOException $e) {
    // Handle any database errors
    echo 'Database Error: ' . $e->getMessage();
    die();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="UIStyle.css">
    <div class="header">
        <a href="home.php"><img class="logo" src="images/OnTheDot.png"><img></a>
    </div>
</head>
<style>
    .table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    th,
    td {
        padding: 50px;
        text-align: left;
    }

    th {
        background-color: #333;
        color: #fff;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ddd;
    }

    a {
        color: #333;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }

    .file-upload-btn {
        display: inline-block;
        position: relative;
        overflow: hidden;
        padding: 0;
        margin-top: 10px;
        width: 150px; /* Adjust the width as needed */
        height: 150px; /* Adjust the height as needed */
        border-radius: 50%;
        background-color: #ccc;
        color: #fff;
        text-align: center;
        cursor: pointer;
        background-color: transparent;
        border: none;
        outline: none;
    }

    .file-upload-btn input[type="file"] {
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-upload-btn img {
        border: 5px solid lightgreen;
        background-color: black;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
</style>

<body>
    <form style="background-color:white;padding-top:0px;" action="update_profile.php" method="POST" enctype="multipart/form-data">
        <fieldset style="background-color:white;">
            <legend>
                <label for="profiles_picture" class="file-upload-btn">
                    <input type="file" name="profiles_picture" id="profiles_picture">
                    <img style="object-fit:contain;" src="<?php echo $result['profile_picture'] ?>" alt="">
                </label>
            </legend>
            <input style="float:right;" type="submit" value="Save">
            <h3>Click On The Profile Picture, Choose An Image And Hit Save.</h3>
        </fieldset>
    </form>
</body>
</html>
