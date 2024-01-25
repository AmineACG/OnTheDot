<?php
session_start();

if (!isset($_SESSION['EMAIL_USER']) || empty($_SESSION['PASSWORD_USER'])) {
    header("Location: login.php");
    exit();
}

// Check if the logged-in user has admin privileges
if ($_SESSION['ROLE'] === 'admin') {
    header("Location: home.php");
    exit();
}

// Check if the admin ID is provided
if (!isset($_GET['id'])) {
    header("Location: profile.php");
    exit();
}

$adminId = $_GET['id'];

// Create a PDO connection to the database
$dsn = 'mysql:host=localhost;dbname=main';
$username = 'root';
$password = '';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Delete the admin from the database
    $query = "DELETE FROM registered_user WHERE ID_USER = :ID_USER AND role = 'admin'";
    $statement = $db->prepare($query);
    $statement->bindParam(':ID_USER', $adminId, PDO::PARAM_INT);
    $statement->execute();

    // Redirect the user back to the admin list page or display a success message
    header("Location: profile.php");
    exit();
} catch (PDOException $e) {
    // Handle any database errors
    echo 'Database Error: ' . $e->getMessage();
    die();
}
?>
