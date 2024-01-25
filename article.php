<?php
session_start();

// Create a PDO connection to the database
$dsn = 'mysql:host=localhost;dbname=main';
$username = 'root';
$password = '';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Check if the article ID is provided
    if (!isset($_GET['id'])) {
        
        exit;
    }

    $articleId = $_GET['id'];
    $query = "SELECT * FROM articles WHERE article_id = :article_id";
    $statement = $db->prepare($query);
    $statement->bindParam(':article_id', $articleId, PDO::PARAM_INT);
    $statement->execute();

    $article = $statement->fetch(PDO::FETCH_ASSOC);
    $loggedInUserEmail = $article['EMAIL_USER'];
        if (!$article) {
        echo "Article ID not found!";
        exit;
    }
    
    $title = $article['title'];
    $content = $article['content'];
    $author = $article['author'];
    $date = $article['date'];
          // Retrieve comments for the current article
    $commentsQuery = "SELECT * FROM comments natural join registered_user WHERE article_id = :article_id";
    $commentsStmt = $db->prepare($commentsQuery);
    $commentsStmt->bindParam(':article_id', $article['article_id']);
    $commentsStmt->execute();
    $comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);

    $loggedInUserEmail = $article['EMAIL_USER'];
    $query = "SELECT role FROM registered_user WHERE EMAIL_USER = :EMAIL_USER";
    $statement = $db->prepare($query);
    $statement->bindParam(':EMAIL_USER', $loggedInUserEmail);
    $statement->execute();

    $query = "SELECT role FROM registered_user";
    $statement = $db->prepare($query);
    $statement->execute();
    $user = $statement->fetchAll(PDO::FETCH_ASSOC);    

    $limit = 5; // Number of articles to display initially
    $offset = 0; // Starting offset

    $moreArticlesQuery = "SELECT * FROM articles ORDER BY date DESC LIMIT :limit OFFSET :offset";
    $moreArticlesStmt = $db->prepare($moreArticlesQuery);
    $moreArticlesStmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $moreArticlesStmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $moreArticlesStmt->execute();

    $moreArticles = $moreArticlesStmt->fetchAll(PDO::FETCH_ASSOC);
    // Retrieve images associated with the article
    $image_ids = array(); // Array to store image IDs
    $image_urls = array(); // Array to store image URLs

    // Retrieve image data from the article_images table
    $sql_images = "SELECT * FROM article_images WHERE article_id = :article_id";
    $stmt_images = $db->prepare($sql_images);
    $stmt_images->bindParam(':article_id', $articleId, PDO::PARAM_INT); // Corrected the variable name here
    $stmt_images->execute();

    while ($row_images = $stmt_images->fetch(PDO::FETCH_ASSOC)) {
        $image_id = $row_images['image_id'];
        $image_url = $row_images['image_url'];

        $image_ids[] = $image_id;
        $image_urls[] = $image_url;
    }
} catch (PDOException $e) {
    // Handle any database errors
    echo 'Database Error: ' . $e->getMessage();
    die();
}

// Close the database connection
$db = null;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="UIStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        // Open the modal and display the clicked image
        function openModal(index) {
            var modal = document.getElementById("myModal");
            var modalImage = document.getElementById("modalImage");

            modal.style.display = "block";
            modalImage.src = "<?php echo $image_urls[0]; ?>"; // Replace with the appropriate PHP code to get the full-size image URL
        }

        // Close the modal
        function closeModal() {
            var modal = document.getElementById("myModal");
            modal.style.display = "none";
        }
    </script>
    <style>
        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            padding: 20px;
            background-color: #f7f7f7;
        }

        h1 {
            margin-top: 0;
        }

        .article {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 20px;
        }

        .article p {
            line-height: 1.5;
            font-weight: 100;

        }
        .more-photos-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 20px;
        }

        .gray-container {
            width: 400px;
            height: 400px;
            overflow: hidden;
        }

        .gray-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .commentario{
            font-size:18px;
            margin-left:10px;
            
        }
        .comment{
            background-color:white;
            margin-bottom:10px;
            border:1px solid black;
            border-bottom-right-radius:50px;
        }
        .email{
            font-weight: 100;
            font-size:22px;
            color:black;
            text-align:center;
            margin-bottom:20px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .modal-content {
            display: block;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
        }

        .modal-content img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
        .comment {
        margin-bottom: 20px;
        background-color: #f9f9f9;
        padding: 10px;
        border-radius: 10px;
    }

    .comment-header {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .profile-picture {
        width: 40px;
        height: 40px;
        overflow: hidden;
        border-radius: 50%;
    }

    .profile-picture img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .username {
        margin-left: 10px;
    }

    .username-text {
        font-weight: bold;
        margin: 0;
    }

    .comment-body {
        margin-left: 50px;
    }

    .comment-text {
        margin: 0;
    }

    .comment-date {
        font-size: 14px;
    }

    .delete-button {
        background-color: #fff;
        border: none;
        color: red;
        cursor: pointer;
        text-decoration: underline;
        padding: 0;
        font-size: 14px;
    }
    </style>
    
</head>
<body>
    <header>
        <a href="home.php"><img class="logo" src="images/OnTheDot.png"></a>
        <h1><?php echo $title; ?></h1>
    </header>

    <div class="article">
        
        <?php
        if (!empty($image_urls)) {
                echo "<h1 style='text-decoration:underline;'>By <i class='fas fa-user'></i> $author Published On The $date</h1> ";
                echo"<br>";
                foreach ($image_urls as $index => $image_url) {
                if($index === 0){
                echo"<div style='height:400px;width100%;margin-bottom:50px;box-shadow: 5px 15px 10px black; ' class='gray-container;'>";
                echo "<img style='width: 100%; height: 100%;margin-bottom:100px;object-fit: cover;

                ' src='" . $image_url . "' alt='Image " . $index . "'>";
                echo "</div>";
                }
            }
            echo "<strong><p style='font-size:20px;'>$content</p>";
            echo "<br><h2 style='padding-top:10px;height:50px;background-color:black;color:white;'>More Photos:</h2>";
            echo "<div class='more-photos-container'>";
            foreach ($image_urls as $index => $image_url) {
                
                echo "<div class='gray-container'>";
                echo "<img src='" . $image_url . "' alt='Image " . $index . "'>";
                echo "</div>";
            }
            echo "</div>";
            
        }?>
        
        <fieldset>
        <div class="comments">
        <h2>Comments:</h2>


    <form method="POST" action="submit_comment.php">
        <input type="hidden" name="article_id" value="<?php echo $article['article_id']; ?>">
        <input type="text" name="comment_text" placeholder="Add your comment" required>
        <input type="submit" value="Submit">
    </form>


<?php foreach ($comments as $comment) : ?>
    <div class='comment'>
        <div class='comment-header'>
            <div class='profile-picture'>
                <img src='<?php echo $comment['profile_picture']; ?>' alt='Profile Picture'>
            </div>
            <div class='username'>
                <p class='username-text'><?php echo $comment['USERNAME_USER']; ?></p>
            </div>
        </div>
        <div class='comment-body'>
            <p class='comment-text'><?php echo $comment['comment_text']; ?></p>
            <p class='comment-date'><?php echo $comment['comment_date']; ?></p>
        </div>
        <?php if ($user !== 'admin'): ?>
            <form style='width:fit-content;background-color:inherit;' method='POST' action='delete_comment.php'>
                <input type='hidden' name='comment_id' value='<?php echo $comment['comment_id']; ?>'>
                <input type='submit' value='Delete' class='delete-button'>
            </form>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
    </div>
    
</body>
</html>
