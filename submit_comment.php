<?php
session_start();

// Check if the user is not logged in and redirect to the login page if necessary
if (!isset($_SESSION['EMAIL_USER']) || empty($_SESSION['PASSWORD_USER'])) {
    header("Location: login.php");
    exit();
}

// Check if the required form fields are submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['article_id'], $_POST['comment_text'])) {
    // Redirect to the appropriate page or display an error message
    header("Location: article.php?article_id=$_POST[article_id]");
    exit();
}

$articleId = $_POST['article_id'];
$commentContent = $_POST['comment_text'];

// Create a PDO connection to the database
$dsn = 'mysql:host=localhost;dbname=main';
$username = 'root';
$password = '';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the article exists
    $articleQuery = "SELECT * FROM articles WHERE article_id = :article_id";
    $articleStmt = $db->prepare($articleQuery);
    $articleStmt->bindParam(':article_id', $articleId);
    $articleStmt->execute();
    $article = $articleStmt->fetch(PDO::FETCH_ASSOC);

    if (!$article) {
        // Article not found
        header("Location: home.php?article_id=$articleId");
        exit();
    }

    // Insert the comment into the database
    $userId = $_SESSION['EMAIL_USER']; // Assuming you have a 'EMAIL_USER' value stored in the session
    $insertQuery = "INSERT INTO comments (article_id, EMAIL_USER, comment_text, comment_date) VALUES (:article_id, :EMAIL_USER, :comment_text, NOW())";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->bindParam(':article_id', $articleId);
    $insertStmt->bindParam(':EMAIL_USER', $userId);
    $insertStmt->bindParam(':comment_text', $commentContent);
    $insertStmt->execute();

    // Redirect back to the article page or refresh the page to display the comment
    header("Location: article.php?article_id=$articleId");
    exit();
} catch (PDOException $e) {
    // Handle any database errors
    echo 'Database Error: ' . $e->getMessage();
    die();
}