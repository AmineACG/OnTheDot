<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="UIStyle.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>Saved Articles</title>
    <script>
        function editArticle(article_id) {
            // Redirect to the edit article page with the article ID as a query parameter
            window.location.href = `edit_article.php?id=${article_id}`;
        }

        function deleteArticle(article_id) {
    // Display a confirmation dialog before deleting the article
    if (confirm("Are you sure you want to delete this article?")) {
        // Redirect to the delete article page with the article ID as a query parameter
        window.location.href = `delete_article.php?article_id=${article_id}`;
    }
}
    </script>
    <style>
        body {
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            margin: 0;
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

        .article img {
            object-fit:cover;
            max-width: 100%;
            border-radius: 0px;
            margin-bottom:  0px;
        }

        .article p {
            line-height: 1.5;
        }

        .article-actions {
            margin-top: 10px;
        }

        .article-actions button {
            margin-right: 10px;
            background-color: #000000;
            color: white;
            padding:10px;
            height:50px;
            width: 120px;
            border: none;
            cursor: pointer;
            margin-bottom:5px;
            font-weight: bold;
        }
        .article-actions button:hover {
            background-color: #3e3e3e;
           
        }
        .image-hover{
            width: 100%; 
            height: 100%;
            
            transition: transform 0.5s ease-in-out, margin-right 1s ease-in-out;
            transform-origin: center;
        }
        .image-hover:hover{
                cursor:pointer;
                display:hidden;
                transform: scale(1.1);
                
                
        }
        .image-container {
        display: flex;
        justify-content: center; /* Align images horizontally */
        position:relative;
        width: 100%;
        box-shadow: 0px 0px 30px rgba(0, 0, 0, 1);
        border-radius: 50px;
    }
    .delete-button {
        
            background-color: #000000;
            color: white;
            padding:10px;
            height:180px;
            border: none;
            cursor: pointer;
            float: right;
            font-weight: bold;
        }

    </style>
</head>
<body>
    <header>
    <a href="home.php"><img class="logo" src="images/OnTheDot.png"><img></a>
        <h1>Saved Articles</h1>
    </header>

    <?php
    session_start();
    // Check if the user is not logged in and redirect to the login page if necessary
    if (!isset($_SESSION['EMAIL_USER']) || empty($_SESSION['PASSWORD_USER'])) {
        header("Location: login.php");
        exit();
    }

    // Retrieve form data 
    $dsn = 'mysql:host=localhost;dbname=main';
    $username = 'root';
    $password = '';

    try {
        // Connect to the database
        $db = new PDO($dsn, $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Retrieve the user's email and password from the session
        $EMAIL_USER = $_SESSION['EMAIL_USER'];
        $PASSWORD_USER = $_SESSION['PASSWORD_USER'];

        // Retrieve the user's articles based on their user ID
        $query = "SELECT * FROM articles WHERE EMAIL_USER = :EMAIL_USER";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':EMAIL_USER', $EMAIL_USER);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Retrieve article data
            $article_id = $row['article_id'];
            $title = $row['title'];
            $author = $row['author'];
            $date = $row['date'];
            $content = $row['content'];
        
            // Retrieve images associated with the article
            $image_ids = array(); // Array to store image IDs
            $image_urls = array(); // Array to store image URLs
        
            // Retrieve image data from the article_images table
            $sql_images = "SELECT * FROM article_images WHERE article_id = :article_id";
            $stmt_images = $db->prepare($sql_images);
            $stmt_images->bindParam(':article_id', $article_id);
            $stmt_images->execute();
        
            while ($row_images = $stmt_images->fetch(PDO::FETCH_ASSOC)) {
                $image_id = $row_images['image_id'];
                $image_url = $row_images['image_url'];
        
                $image_ids[] = $image_id;
                $image_urls[] = $image_url;
            }
        
            // Display the article and associated images
            
            echo "<div class='article'>";
            echo "<fieldset>";
            echo "<legend style='text-align:center;'><h2 style='margin-bottom:0px;'>$title</h2></legend>";

            echo "<h2>Author: $author</h2>";
            echo "<h2>Date: $date</h2>";
            echo "<h4>$content</h4>";

            echo "</fieldset>";
            
            // Display images if available
            if (!empty($image_urls)) {
                echo "<div class='image-container'>";
                foreach ($image_urls as $index => $image_url) {
                    echo"<div style='height:200px;width:350px;margin-right:15px;' class='image-container;'>";
                    echo "<img class='image-hover' src='" . $image_url . "' alt='Image " . $index . "'>";
                    echo "</div>";
                }
                echo "</div>";
            }
            // Article actions: Edit and Delete buttons
            echo '<div class="article-actions">';
            echo "<h2><button onclick='editArticle($article_id)'>Edit Article</button>";
            echo "<button onclick='deleteArticle($article_id)'>Delete Article</button></h2>";
            echo '</div>';
            echo '</div>';
            echo "</div>";
        }

            
        }catch (PDOException $e) {
            // Handle any database errors
            echo 'Database Error: ' . $e->getMessage();
            die();
        }
        ?>
    </body>
    </html>