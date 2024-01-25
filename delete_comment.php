<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['EMAIL_USER'])) {
    // Redirect the user to the login page or display an error message
    header("Location: login.php");
    exit;
}

// Create a PDO connection to the database
$dsn = 'mysql:host=localhost;dbname=main';
$username = 'root';
$password = '';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle any database errors
    echo 'Database Error: ' . $e->getMessage();
    die();
}

// Check if the comment ID is provided
if (!isset($_POST['comment_id'])) {
    // Redirect the user or display an error message
    echo "Comment Not Found!";
    exit;
}

$commentId = $_POST['comment_id'];

// Delete the comment from the database
$query = "DELETE FROM comments WHERE comment_id = :comment_id";
$statement = $db->prepare($query);
$statement->bindParam(':comment_id', $commentId, PDO::PARAM_INT);
$statement->execute();

// Redirect the user back to the article page or display a success message
header("Location: article.php?id=$articleId");
exit;
?>
