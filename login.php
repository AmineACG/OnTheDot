<?php
session_start();
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the login data
    $EMAIL_USER = $_POST['EMAIL_USER'];
    $PASSWORD_USER= $_POST['PASSWORD_USER'];
    // Create a PDO connection to the database
    $dsn = 'mysql:host=localhost;dbname=main';
    $username = 'root';
    $password = '';
    try {
        $db = new PDO($dsn, $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        // Check if the login credentials are valid
        $sql = "SELECT * FROM registered_user WHERE EMAIL_USER = :EMAIL_USER AND PASSWORD_USER = :PASSWORD_USER";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':EMAIL_USER', $EMAIL_USER);
        $stmt->bindParam(':PASSWORD_USER', $PASSWORD_USER);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            // Login successful
            $_SESSION['EMAIL_USER'] = $EMAIL_USER;
            $_SESSION['PASSWORD_USER'] = $PASSWORD_USER;
            
            // Redirect to home page if the session variables are set
            if (isset($_SESSION['EMAIL_USER']) && isset($_SESSION['PASSWORD_USER'])) {
                header("Location: home.php");
                exit();
            }
        } else {
            // Invalid login credentials
            echo "<div id='success-message' style='background-color:rgba(236, 38, 38, 0.8);'>Invalid Email Or Password !</div>";
        }
    } catch (PDOException $e) {
        // Handle any database errors
        echo 'Database Error: ' . $e->getMessage();
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OnTheDot</title>
    <link rel="stylesheet" href="UIStyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    
</head>
<script>
// Select the success message element
const successMessage = document.getElementById('success-message');

// Add a class to show the message and initiate the fade-in effect
successMessage.classList.add('show');

// Add a class to hide the message after 5 seconds and initiate the fade-out effect
setTimeout(function() {
  successMessage.classList.add('hide');
  setTimeout(function() {
    successMessage.parentNode.removeChild(successMessage);
  }, 2000);
}, 5000);
    function togglePasswordVisibility() {
    var passwordInput = document.getElementById("PASSWORD_USER");
    var eyeIcon = document.getElementById("eyeIcon");

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.classList.remove("fa-eye");
        eyeIcon.classList.add("fa-eye-slash");
    } else {
        passwordInput.type = "password";
        eyeIcon.classList.remove("fa-eye-slash");
        eyeIcon.classList.add("fa-eye");
    }
    }
     // Get all slideshow images
     const slideshowImages = document.querySelectorAll('.slideshow-container img');
    let currentIndex = 0;

    // Function to fade in the current image
    function showImage(index) {
        // Hide all images
        slideshowImages.forEach(image => {
            image.style.display = 'none';
            image.classList.remove('fade');
        });

        // Show the current image
        slideshowImages[index].style.display = 'block';
        slideshowImages[index].classList.add('fade');
    }

    // Function to update the slideshow
    function updateSlideshow() {
        // Show the current image
        showImage(currentIndex);

        // Increment the current index
        currentIndex++;

        // Reset index if it exceeds the number of images
        if (currentIndex >= slideshowImages.length) {
            currentIndex = 0;
        }

        // Call the updateSlideshow function again after 5 seconds
        setTimeout(updateSlideshow, 5000);
    }

    // Call the updateSlideshow function to start the slideshow
    updateSlideshow();
</script>
<style>
    @media screen and (max-width: 320px) {
        .container {
            padding-right: 20px;
            padding-left: 20px;
        }

        /* Adjust form width for mobile devices */
        .login-form {
            width: 100%;
        }

        /* Increase the height of input fields and font size for mobile devices */
        .form-group input {
            height: 40px;
            font-size: 16px;
        }

        /* Adjust the size and position of the eye icon for mobile devices */
        #eyeIcon {
            right: 10px;
        }

        /* Adjust the margin and padding of the forgot password link for mobile devices */
        .form-group a {
            margin-top: 5px;
            padding-left: 5px;
        }

        /* Adjust the margin of the login button for mobile devices */
        button[type="submit"] {
            margin-top: 10px;
        }

        /* Adjust the margin and font size of the "Don't Have An Account" text for mobile devices */
        .antimsp {
            margin-top: 20px;
            font-size: 14px;
        }
    }
    .logo{
	color: #fff;
    background-color: White;
	text-decoration: none;
	border: 1px solid transparent;
	padding:5px;
	transition: all 1s ease; /* adds a smooth transition */
  }
  
    .logo:hover {
	border-color: black; /* changes the border color on hover */
	border-radius: 5px;
    padding:15px;
	color: black;
    cursor: pointer;
  }
  #togglePassword {
  position: relative;
  cursor: pointer;
    }

    #eyeIcon {
    position: absolute;
    top: 50%;
    right: 5px;
    transform: translateY(-50%);
    }
    .hover-scale {
    display: block;
    width: 100%;
    height: auto;
    transition: transform 1s ease-in-out, box-shadow 1s ease-in-out;
    transform-origin: center;
    }

    .hover-scale:hover {
        transform: scale(1.1);
        box-shadow: -30px 30px 10px rgba(0, 0, 0, 0.3);
    }

    .fade {
        animation-name: fade;
        animation-duration: 1s;
        animation-timing-function: ease-in-out;
    }

    .image-container {
        width: 100%;
        box-shadow: -30px 30px 10px rgba(0, 0, 0, 0.3);
    }

    @keyframes fade {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }
    
    </style>
<body style="background-color:#EDEDED;">
    <header style="background-color:black;">
        <img class="logo" src="images/OnTheDot.png" alt="Logo">
    </header>
    <main style="padding: 0px;">
        <div style="border-right:2px solid;border-bottom:2px solid;margin:0px;padding-right:50%;" class="container">   
            <h1>Bienvenue dans votre espace d'actualités personnalisé.<br>Explorez les rubriques qui vous intéressent le plus et plongez-vous !</h1>
            <form style="width:600px;height:400px;background-color:white;" class="login-form" action="login.php" method="POST">
                <div class="form-group">
                    <label for="EMAIL_USER"><strong>Email Address</strong></label>
                    <input style="height:50px;" type="text" id="EMAIL_USER" name="EMAIL_USER" required>
                </div>
                <div class="form-group">
                    <label for="PASSWORD_USER"><strong>Password</strong></label>
                    <input style="height:50px;" type="password" id="PASSWORD_USER" name="PASSWORD_USER" required>
                    <span id="togglePassword" onclick="togglePasswordVisibility()">
                        <i class="fa fa-eye" id="eyeIcon"></i>
                    </span>
                    <a href="#"><h4>Mot De Passe Oublié?</h4></a>
                </div>
                <button type="submit">Log in</button><br>
                <div style="width: 560px;" class="line-hor"></div>
                <div class="antimsp">
                    <label style="font-size:16px;positon:relative;"><strong>Don't Have An Account ? </strong><a href="Sign Up.php"><strong><i>Sign Up</i></strong></a>
                </div>
            </form>
        </div>
        <div style="background-color: inherit; margin-left: 100px;">
            <br>
            <div class="slideshow-container">
                <div class="image-container">
                    <img class="hover-scale fade" style="width: 100%;" src="images/OnTheDate1.png" alt="Slideshow Image">
                </div>
            </div>
        </div>
    <main>
</body>
</html> 