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
}catch(PDOException $e) {
  echo 'Database Error: ' . $e->getMessage();
  die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="UIStyle.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <title>Document</title>
  <style>
   html, body {
    height: 100%;
    margin: 0;
    padding: 0;
  }

  .columns {
  display: flex;
  flex-direction: row;
  height: 100%;
}

.column {
  font-size: 22px;
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-start; /* Align content at the top */
  background-size: cover;
  background-position: center;
  cursor: pointer;
  position: relative;
}

.column-title {
  background-color: rgba(255, 255, 255, 0);
 /* Add the text-shadow property for letter shadows */
  opacity: 0; /* Initially hide the title text */
  transition: opacity 0.3s ease; /* Add transition effect for opacity */
  font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
  color: white;
  font-size: 38px;
  font-weight: bold;
  text-align: center;
  margin-top: 10px;
}


.column:hover .column-title {
  display: block; /* Show the column title on hover */
  opacity: 1; /* Show the title text on hover */

}


  .column::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0);
    transition: background-color  0.3s ease;
  }

  .column:hover::before {
    background-color: rgba(255, 255, 255, 0.315);
  }
  .Description{
    height:300px;
  }
  .Description h1{
    float:right;
    font-size:66px;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
  }
 
  </style>
</head>
<body>
  <header style="background-color: rgb(0, 0, 0);">
    <a href="home.php"><img class="logo" src="images/OnTheDot.png"></a>
    <h1>Categories</h1> 
  </header>
  <div class="Description">
    <h1>Discover More & Precisely Choose<br>What Interests You<br>The Most !</h1>
  </div>
  <div class="columns">
    <a href="home.php?category=1" class="column" style="background-image: url('images/cateducate.png');">
      <h1 style="color:black" class="column-title">Education</h1>
    </a>
    <a href="home.php?category=2" class="column" style="background-image: url('images/catmusic.png');">
      <h1 style="color:black" class="column-title">Music</h1>
    </a>
    <a href="home.php?category=3" class="column" style="background-image: url('images/AI-chip.png');">
      <h1 style="color:black" class="column-title">Technology</h1>
    </a>
    <a href="home.php?category=4" class="column" style="background-image: url('images/catmoney.png');">
      <h1 style="color:black" class="column-title">Economy</h1>
    </a>
   
    <a href="home.php?category=5" class="column" style="background-image: url('images/catsport.png');">
      <h1 style="color:black" class="column-title">Sports</h1>
    </a>
    <a href="home.php?category=6" class="column" style="background-image: url('images/catgame.png');">
      <h1 style="color:black" class="column-title">Games News</h1>
    </a>
    <a href="home.php?category=7" class="column" style="background-image: url('images/catpolitic.png');">
      <h1 style="color:black" class="column-title">Politics</h1>
    </a>
    <a href="home.php?category=8" class="column" style="background-image: url('images/catlife.png');">
      <h1 style="color:black" class="column-title">Life Style</h1>
    </a>
    
    <!-- Add more columns here -->
  </div>

  <script>
    // Fade-in animation for the columns
    window.addEventListener('DOMContentLoaded', () => {
      const columns = document.querySelector('.columns');
      columns.style.opacity = '1';
    });
  </script>
</body>
</html>
