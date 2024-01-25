<?php
session_start();
// Check if the user is not logged in and redirect to the login page if necessary
if (!isset($_SESSION['EMAIL_USER']) || empty($_SESSION['PASSWORD_USER'])) {
    header("Location: login.php");
    exit();
}

// Check if the article ID is provided
if (!isset($_GET['article_id']) || empty($_GET['article_id'])) {
    header("Location: myArticles.php");
    exit();
}

// Retrieve the article ID from the query string
$article_id = $_GET['article_id'];

// Create a PDO connection to the database
$dsn = 'mysql:host=localhost;dbname=main';
$username = 'root';
$password = '';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retrieve the email of the logged-in user
    $email = $_SESSION['EMAIL_USER'];

    // Check if the logged-in user owns the article
    $query = "SELECT * FROM articles WHERE article_id = :article_id AND EMAIL_USER = :EMAIL_USER";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':article_id', $article_id);
    $stmt->bindParam(':EMAIL_USER', $email);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        // Article not found or user doesn't own the article
        header("Location: myArticles.php");
        exit();
    }

    // Retrieve the image IDs and filenames associated with the article
    $imageIds = [];
    $imageFilenames = [];
    $imageQuery = "SELECT image_id, image_url FROM article_images WHERE article_id = :article_id";
    $imageStmt = $db->prepare($imageQuery);
    $imageStmt->bindParam(':article_id', $article_id);
    $imageStmt->execute();
    while ($imageRow = $imageStmt->fetch(PDO::FETCH_ASSOC)) {
        $imageIds[] = $imageRow['image_id'];
        $imageFilenames[] = $imageRow['image_url'];
    }

    // Delete the comments associated with the article
    $deleteCommentsQuery = "DELETE FROM comments WHERE article_id = :article_id";
    $deleteCommentsStmt = $db->prepare($deleteCommentsQuery);
    $deleteCommentsStmt->bindParam(':article_id', $article_id);
    $deleteCommentsStmt->execute();

    // Delete the article images associated with the article and their physical files
    $deleteImagesQuery = "DELETE FROM article_images WHERE article_id = :article_id";
    $deleteImagesStmt = $db->prepare($deleteImagesQuery);
    $deleteImagesStmt->bindParam(':article_id', $article_id);
    $deleteImagesStmt->execute();

    foreach ($imageFilenames as $filename) {
        $filepath = "images/" . $filename; // Replace with the actual file path
        if (file_exists($filepath)) {
            unlink($filepath); // Delete the file from the server
        }
    }

    // Delete the article from the database
    $deleteQuery = "DELETE FROM articles WHERE article_id = :article_id AND EMAIL_USER = :EMAIL_USER";
    $deleteStmt = $db->prepare($deleteQuery);
    $deleteStmt->bindParam(':article_id', $article_id);
    $deleteStmt->bindParam(':EMAIL_USER', $email);
    $deleteStmt->execute();

    // Redirect to the articles page with a success message
    header("Location: myArticles.php");
    exit();
} catch (PDOException $e) {
    // Handle any database errors
    echo 'Database Error: ' . $e->getMessage();
    die();
}
?>
<html>
<link rel="stylesheet" href="UIStyle.css"> 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<head>
    <title>Deleted</title>
    <style>
        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f7f7f7;
        }
        h1 {
            margin-top: 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Article Deleted</h1>
</body>
</html>
