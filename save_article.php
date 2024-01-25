<?php
session_start();
if (!isset($_SESSION['EMAIL_USER']) || empty($_SESSION['PASSWORD_USER'])) {
    header("Location: login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $date = $_POST['date'];
    $content = nl2br($_POST['content']); // Preserve line breaks and spaces
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

        // Insert new article
        $sql = "INSERT INTO articles (title, author, date, content, EMAIL_USER, category_id) VALUES (:title, :author, :date, :content, :EMAIL_USER, :category_id)";

        // Prepare the SQL statement (REGEX protection from SQL injections)
        $stmt = $db->prepare($sql);
        $email = $_SESSION['EMAIL_USER'];

        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':author', $author);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':EMAIL_USER', $email);
        $stmt->bindParam(':category_id', $category); // Add this line

        // Execute the SQL statement
        $stmt->execute();

        // Retrieve the inserted article's ID
        $article_id = $db->lastInsertId();

        // Insert the image URLs into the article_images table
        for ($i = 0; $i < count($image_paths); $i++) {
            $image_path = $image_paths[$i];

            $sql = "INSERT INTO article_images (article_id, image_url) VALUES (:article_id, :image_url)";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':article_id', $article_id);
            $stmt->bindParam(':image_url', $image_path);
            $stmt->execute();
        }

        // Redirect back to the form or to a success page
        header("Location: Home.php");
        exit();
    } catch (PDOException $e) {
        // Handle any database errors
        echo 'Database Error: ' . $e->getMessage();
        die();
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
<title>Upload Your Article To Our Page!</title>
</head>
<body>
<div class="topnav">
  
    <?php if (isset($_SESSION['EMAIL_USER']) && !empty($_SESSION['PASSWORD_USER'])): ?>
      <a href="home.php">Home <i class="fas fa-home"></i></a>
      <a href="categories.php">Categories</a>
      <a style="float:right;" href="profile.php">Profile <i class="fas fa-user"></i></a>
      <?php else: ?>
      <a href="login.php" style="float:right">Se Connecter</a>
    <?php endif; ?>
    </div>
</div>
<main>
    <form action="save_article.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="article_id" value="<?php echo $article_id; ?>">
  
  <label for="title">Title:</label>
  <input type="text" name="title" id="title" value="" required>
  
  <label for="author">Author:</label>
  <input type="text" name="author" id="author" value="<?php echo $_SESSION['EMAIL_USER']?>" required>

  <label for="date">Date:</label>
  <input type="date" name="date" id="date" value="<?php echo date('Y-m-d');?>" required>

  <br><br><label for="category">Category:</label>
  <input type="text" name="category_id" id="category_id" list="category-list" required>
  <datalist id="category-list">
  <option value="1">Education</option>
  <option value="2">Music</option>
  <option value="3">Technology</option>
  <option value="4">Economy</option>
  <option value="5">Sports</option>
  <option value="6">Games News</option>
  <option value="7">Politics</option>
  <option value="8">Life Style</option>
  </datalist><br>
  <label for="content">Content:</label>
  <textarea name="content" id="content" rows="4" style="resize: none;" required></textarea> 
  <br>
  <button style="color:white;font-size:20px;width:600px;" type="submit">Save Article</button>
  
  <div style="border-radius:10px;width: 50px;height: 50px;background-color:black;border:2px solid black;"class="image-input">
    <input type="file" name="images[]" id="image" style="display:none" multiple>
    <label for="image" class="image-label">
      <div class="square-icon">
        <i class="fas fa-plus-square fa-2x"></i>
      </div>
    </label>
  </div>
</form>
</main>
</body>
</html>


