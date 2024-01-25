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
  // Retrieve articles from your database based on the selected category
  $selectedCategory = isset($_GET['category']) ? $_GET['category'] : null;
  $searchKeyword = isset($_GET['search']) ? $_GET['search'] : null;
  $articles = [];

  // Filter articles if a category or search keyword is selected
  if ($selectedCategory) {
    $sql = "SELECT * FROM articles WHERE category_id = :category_id ORDER BY RAND()";
      $stmt = $db->prepare($sql);
      $stmt->bindParam(':category_id', $selectedCategory);
  } elseif ($searchKeyword) {
    $sql = "SELECT * FROM articles WHERE title LIKE :searchKeyword OR content LIKE :searchKeyword ORDER BY RAND()";
      $stmt = $db->prepare($sql);
      $searchKeyword = "%{$searchKeyword}%";
      $stmt->bindParam(':searchKeyword', $searchKeyword);
  } else {
    $sql = "SELECT * FROM articles ORDER BY RAND()";
      $stmt = $db->prepare($sql);
  }

  $stmt->execute();
  $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

  $userEmail = $_SESSION['EMAIL_USER'];

  $sql = "SELECT * FROM registered_user WHERE EMAIL_USER = :EMAIL_USER";
  $stmt = $db->prepare($sql);
  $stmt->bindParam(':EMAIL_USER', $userEmail);
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $ROLE = $result['role'];

  $sqlImages = "SELECT * FROM article_images";
  $stmtImages = $db->prepare($sqlImages);
  $stmtImages->execute();
  $imageUrls = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <title>OnTheDot.</title>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var images = document.querySelectorAll('card:last-of-type .read-overlay');
        var index = 0;

        function showNextImage() {
            images[index].style.display = 'none';
            index = (index + 1) % images.length;
            images[index].style.display = 'inline-block';
        }

        showNextImage();
        setInterval(showNextImage, 5000);
    });
    document.addEventListener('DOMContentLoaded', function () {
      var images = document.querySelectorAll('.image-overlay');
      var index = 0;

      function showNextImage() {
          images[index].style.display = 'none';
          index = (index + 1) % images.length;
          images[index].style.display = 'inline-block';
      }

      showNextImage();
      setInterval(showNextImage, 5000);
  });
    </script>
<style>
  * {
    box-sizing: border-box;
  }
  h2{
    text-decoration:underline;
  }

  body {
    font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    padding: 10px;
    background:#333;
  }

  /* Header/Blog Title */
  .header {
    padding: 10px;
    text-align: center;
    background: white;
  }

  .header h1 {
    margin: 0px;
    font-size: 50px;
  }

  /* Style the top navigation bar */
  .topnav {
    overflow: hidden;
    background-color: #333;
  }

  /* Style the topnav links */
  .topnav a {
    float: left;
    font-size:22px;
    display: block;
    color: #f2f2f2;
    text-align: center;
    padding: 14px 15px;
    text-decoration: none;
  }

  /* Change color on hover */
  .topnav a:hover {
    background-color: #ddd;
    color: black;
  }

  /* Create two unequal columns that floats next to each other */
  /* Left column */
  .main {  
    padding-right:5px; 
    float: left;
    width: 70%;
  }

  /* Right column */
  .rightcolumn {
    float: left;
    width: 30%;
    background-color: #f1f1f1;
    padding-left: 20px;
  }

  /* Fake image */
  
  /* Add a card effect for articles */
  .card {
    background-color: rgb(233, 233, 233);
    padding: 20px;
    margin-top: 5px;
  }

  /* Clear floats after the columns */
  .row::after {
    content: "";
    display: table;
    clear: both;
  }

  /* Footer */
  footer {
  background-color: #f5f5f5;
  padding: 20px 0;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  color:white;

}

.footer-left,
.footer-right {
  width: 45%;
  
}

.footer-left h4,
.footer-right h4 {
  margin-bottom: 10px;
}

.footer-bottom {
  background-color: #ebebeb;
  padding: 10px 0;
  text-align: center;
}

  /* Responsive layout - when the screen is less than 800px wide, make the two columns stack on top of each other instead of next to each other */
  @media screen and (max-width: 800px) {
    .main, .rightcolumn {   
      width: 100%;
      padding: 0;
    }
  }
  a{
    color:black;
  }
  /* Responsive layout - when the screen is less than 400px wide, make the navigation links stack on top of each other instead of next to each other */
  @media screen and (max-width: 400px) {
    .topnav a {
      float: none;
      width: 100%;
    }
         }
        .gray-container {
          overflow:hidden;
          background-color: #aaa;
          width: 100%;
          height:300px;
          
        }

        .gray-container img{
          height: 100%;
          width: 100%;
          object-fit: cover;

        }
        .read-overlay {
        position: relative;
        width: 100%;
        height: 100%;
        object-fit: cover;
        overflow: hidden;
        }

        .overlay {
        position: absolute; /* Add this line */
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6));
        opacity: 0;
        transition: opacity 0.3s ease;
        background-color: #ffffff00;
        }

        .overlay a {
        position: absolute;
        font-size: 24px;
        top: 3%;
        left:3%;
        transform: translate(0%, 50%);
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        }

        .read-overlay:hover .overlay {
        opacity: 1;
        }
  .undertitle{
    background-color: #bdbdbd;
    padding:5px;
    font-weight: bold;
    margin-bottom: 3px;
  }
  .undertitle:hover{
    cursor: pointer;
    background-color: #ddd;
  }
  .search-container{
    height: 35px;
    width: 100%;
  }
  .search-container button{
    width: 10%;
    height: 30px;
    background-color: #333;
  }
  .search-container input{
    float: left;
    width: 75%;
    height: 30px;
    padding-left: 10px;
    font-size: 16px;
    border: 1px solid #999999;
  }
  .logo{
    background-color: #fff;
    text-decoration: none;
    border: 1px solid transparent;
    padding:5px;
    transition: all 1s ease; /* adds a smooth transition */
  }

  .logo:hover {
    border-color: black; /* changes the border color on hover */
    border-radius: 5px;
    background-color: white;
    padding:10px;
    color: black;
    cursor: pointer;
  }
</style>

</head>
<body>

<div class="header">
<a href="home.php"><img class="logo" src="images/OnTheDot.png"><img></a>
  <p style="font-size:20px;"><?php echo "Welcome ".$result['NAME_USER'];?>, Explore the sections that interest you the most and immerse yourself!</p>
</div>

<div class="topnav">
  
    <?php if (isset($_SESSION['EMAIL_USER']) && !empty($_SESSION['PASSWORD_USER'])): ?>
      <a href="home.php">Home <i class="fas fa-home"></i></a>
      <a href="categories.php">Categories</a>
      <?php if ($ROLE == 'admin'): ?>
        <a href="save_article.php">New Article <i class="fas fa-pencil-alt"></i></a>
        <?php endif; ?>
      <a href="profile.php">Profile <i class="fas fa-user"></i></a>
      <?php else: ?>
      <a href="login.php" style="float:right">Se Connecter</a>
    <?php endif; ?>
    </div>
</div>
<div class="row">
<div class="main">
  <?php for ($i = 0; $i < count($articles); $i += 2): ?>
    <div class="row">
      <div class="card">
        <?php $article = $articles[$i]; ?>
        <a style="color:black;"href="article.php?id=<?php echo $article['article_id']; ?>"><h2><?php echo $article['title']; ?></h2></a>
        <h5>By <i class="fas fa-user"></i> <a style="color: black;" href="#"><?php echo $article['author']; ?></a> Published <?php echo $article['date']; ?></h5>
        <?php $articleImages = array_filter($imageUrls, function($image) use ($article) {              
          return $image['article_id'] == $article['article_id'];
        }); ?>
        <?php if (!empty($articleImages)): ?>
          <div class="gray-container">
            <?php foreach ($articleImages as $index => $image): ?>
              <div class="read-overlay">
                <img style='width: 100%; height: 100%;' src='<?php echo $image['image_url']; ?>' alt='Image <?php echo $index + 1; ?>'>
                
                <div class="overlay">
                <a href="article.php?id=<?php echo $article['article_id']; ?>"><?php echo $article['title']; ?></a>
                </div> 
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <?php if ($i + 1 < count($articles)): ?>
    <div class="card">
        <?php $article = $articles[$i + 1]; ?>
        <a style="color:black;"href="article.php?id=<?php echo $article['article_id']; ?>"><h2><?php echo $article['title']; ?></h2></a>
        <h5>By <i class="fas fa-user"></i> <a style="color: black;" href="#"><?php echo $article['author']; ?></a> Published <?php echo $article['date']; ?></h5>
        <?php $articleImages = array_filter($imageUrls, function($image) use ($article) {              
            return $image['article_id'] == $article['article_id'];
        }); ?>
        <?php if (!empty($articleImages)): ?>
            <div class="gray-container">
                <?php foreach ($articleImages as $index => $image): ?>
                    <div class="read-overlay">
                        <img style='width: 100%; height: 100%;' src='<?php echo $image['image_url']; ?>' alt='Image <?php echo $index + 1; ?>'>
                        <div class="overlay">
                            <a href="article.php?id=<?php echo $article['article_id']; ?>"><?php echo $article['title']; ?></a>
                        </div> 
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>
    </div>
  <?php endfor; ?>
</div>

      <div class="rightcolumn" style=""> 
        <div style="padding-top:0px;" class="card">
        <div class="search-container">
          <form action="home.php" method="GET">
          <h3>Looking For Something Specific?</h3>
            <input type="text" placeholder="Search" name="search">
            
            <button style="background-color:white;"type="submit"><i><div class="fas fa-search"></div></i></button>
          </form>
        </div>
        </div>
        <div class="card">   
          <h2 style ="font-size:30px;text-align:center;background-color:black;color:white;padding-top:30px;padding-bottom:30px;text-decoration:none;">Recent News</h2>

                  <?php foreach ($articles as $article): ?>
              <?php
                $articleDate = strtotime($article['date']);
                $threeDaysAgo = strtotime('-3 days');

                if ($articleDate > $threeDaysAgo):
              ?>  
                <div style="background-color:black;color:white;" class="card">
                <a style="color:white;"href="article.php?id=<?php echo $article['article_id']; ?>"><h2><?php echo $article['title']; ?></h2></a>
                <h5 style="color:white;">By <i class="fas fa-user"></i> <a style="color: white;" href="#"><?php echo $article['author']; ?></a> Published <?php echo $article['date']; ?></h5>
                <?php $articleImages = array_filter($imageUrls, function($image) use ($article) {              
                return $image['article_id'] == $article['article_id'];
          }); ?>
          <?php if (!empty($articleImages)): ?>
            <div class="gray-container">
              <?php foreach ($articleImages as $index => $image): ?>
                <div class="read-overlay">
                  <img style='width: 100%; height: 100%;' src='<?php echo $image['image_url']; ?>' alt='Image <?php echo $index + 1; ?>'>
                  <div class="overlay">
                  <a href="article.php?id=<?php echo $article['article_id']; ?>">Discover More...</a>
                  </div> 
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>     
        </div>
        <div class="section">
      <div class="card">
        <h3>Popular Posts</h3>
        <div class="card">

        </div>
</div>
</div>
</div>

<div class="footer-container">
    <div class="footer-left">
      <h4>About Us</h4>
      <p>Welcome to our news website, your trusted source for timely and reliable news coverage. At OnTheDot, we are committed to delivering high-quality journalism and keeping you informed about the latest happenings across various topics.</p>
    </div>
    <div class="footer-right">
      <h4>Contact Us</h4>
      <p>Email: med.amine.birje@example.com</p>
      <p>Phone: 0772441117</p>
    </div>
  </div>
  <div class="footer-bottom">
    <p>&copy; 2023 OnTheDot. All rights reserved.</p>
  </div>
</footer>

</body>
</html>
