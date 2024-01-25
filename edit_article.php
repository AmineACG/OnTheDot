<?php
session_start();
if (!isset($_SESSION['EMAIL_USER']) || empty($_SESSION['PASSWORD_USER'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $article_id = $_POST['article_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $date = $_POST['date'];
    $content = $_POST['content'];
    $category = $_POST['category_id'];

    // Handle image upload
    $image_paths = array(); // Array to store image paths

    // Check if any images are uploaded
    if (!empty($_FILES['images']['name'][0])) {
        $image_files = $_FILES['images']; // Get the uploaded image files

        // Loop through each uploaded image
        for ($i = 0; $i < count($image_files['name']); $i++) {
            $image = $image_files['name'][$i]; // Get the image filename
            $image_temp = $image_files['tmp_name'][$i]; // Get the temporary image path
            $image_path = 'images/' . $image; // Define the image path where it will be saved

            move_uploaded_file($image_temp, $image_path); // Move the uploaded image to the desired location

            $image_paths[] = $image_path; // Add the image path to the array
        }
    }

    // Create a PDO connection to the database
    $dsn = 'mysql:host=localhost;dbname=main';
    $username = 'root';
    $password = '';

    try {
        $db = new PDO($dsn, $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Database connection successful!";

        // Update existing article
        $sql = "UPDATE articles SET title = :title, author = :author, date = :date, content = :content, category_id = :category_id WHERE article_id = :article_id AND EMAIL_USER = :EMAIL_USER";

        // Prepare the SQL statement (REGEX protection from SQL injections)
        $stmt = $db->prepare($sql);
        $email = $_SESSION['EMAIL_USER'];

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':EMAIL_USER', $email);
        $stmt->bindParam(':category_id', $category);
        $stmt->bindParam(':article_id', $article_id);

        // Execute the SQL statement
        $stmt->execute();

        // Insert the image URLs into the article_images table
        for ($i = 0; $i < count($image_paths); $i++) {
            $image_path = $image_paths[$i];

            $sql = "INSERT INTO article_images (article_id, image_url) VALUES (:article_id, :image_url)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':article_id', $article_id);
            $stmt->bindParam(':image_url', $image_path);
            $stmt->execute();
        }

        // Redirect to a success page
        header("Location: myArticles.php");
        exit();
    } catch (PDOException $e) {
        // Handle any database errors
        echo 'Database Error: ' . $e->getMessage();
        die();
    }
} else {
    // Retrieve the article ID from the URL
    if (isset($_GET['article_id'])) {
        $article_id = $_GET['article_id'];

        // Create a PDO connection to the database
        $dsn = 'mysql:host=localhost;dbname=main';
        $username = 'root';
        $password = '';

        try {
            $db = new PDO($dsn, $username, $password);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Fetch the article details from the database
            $sql = "SELECT * FROM articles WHERE article_id = :article_id AND EMAIL_USER = :EMAIL_USER";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':article_id', $article_id);
            $stmt->bindParam(':EMAIL_USER', $_SESSION['EMAIL_USER']);
            $stmt->execute();

            $article = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($article) {
                // Set the article details
                $title = $article['title'];
                $author = $article['author'];
                $date = $article['date'];
                $content = $article['content'];
                $category = $article['category_id'];
            } else {
                // Article not found or not owned by the user
                // Handle the error or redirect to an appropriate page
                header("Location: myArticles.php");
                exit();
            }
        } catch (PDOException $e) {
            // Handle any database errors
            echo 'Database Error: ' . $e->getMessage();
            die();
        }
    } else {
        // No article ID provided in the URL
        // Handle the error or redirect to an appropriate page
        header("Location: myArticles.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="UIStyle.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
<title>Edit Article</title>
</head>
<body>
<header>
    <a href="home.php"><img class="logo" src="images/OnTheDot.png"><img></a>
    <nav>
        <ul>
            <?php 
            if (isset($_SESSION['EMAIL_USER']) && !empty($_SESSION['PASSWORD_USER'])): ?>
                <li><a href="Home.php"><i class="fas fa-home"> Home</a></i></li>
                <li><a href="save_article.php"><i class="fas fa-pencil-alt"> Submit Article</a></i></li>
                <li>
                    <a href="#" onclick="toggleMenu()">
                        <i class="fas fa-user"></i>
                    </a>
                </li>
                <script>
                function toggleMenu() {
                    var menu = document.getElementById("user-menu");
                    if (menu.style.display === "none") {
                    menu.style.display = "block";
                    } else {
                    menu.style.display = "none";
                    }
                }
                function confirmLogOut() {
                    alert("Are You Sure You Want To Log Out");
                }
                </script>
            <?php else: ?>        
                <li><a href="login.php">Se Connecter</a></i></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>
    <form action="edit_article.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $title; ?>" required>
        <br>
        <label for="author">Author:</label>
        <input type="text" id="author" name="author" value="<?php echo $author; ?>" required>
        <br>
        <label for="date">Date:</label>
        <input type="date" id="date" name="date" value="<?php echo $date; ?>" required>
        <br>
        <label for="content">Content:</label>
        <textarea id="content" name="content" required><?php echo $content; ?></textarea>
        <br>
        <label for="category">Category:</label>
        <select id="category" name="category_id" required>
            <option value="1" <?php if ($category == 1) echo 'selected'; ?>>Category 1</option>
            <option value="2" <?php if ($category == 2) echo 'selected'; ?>>Category 2</option>
            <option value="3" <?php if ($category == 3) echo 'selected'; ?>>Category 3</option>
        </select>
        <br>
        <label for="images">Images:</label>
        <input type="file" id="images" name="images[]" multiple>
        <br>
        <input type="submit" value="Save Changes">
    </form>
</main>
</body>
</html>
