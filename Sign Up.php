<?php
session_start();
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data 
    $ID_USER = $_POST['ID_USER'];
    $NAME_USER = $_POST['NAME_USER'];
    $LASTNAME_USER = $_POST['LASTNAME_USER'];
    $USERNAME_USER = $_POST['USERNAME_USER'];
    $PASSWORD_USER = $_POST['PASSWORD_USER'];
	$EMAIL_USER = $_POST['EMAIL_USER'];
    $ROLE = $_POST['ROLE'];
    $profile_picture = $_POST['profile_picture'];
    // Create a PDO connection to the database
    $dsn = 'mysql:host=localhost;dbname=main';
    $username = 'root';
    $password = '';
    
    try {
        $db = new PDO($dsn, $username, $password);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Database connection successful!";
        
        // Check if the email already exists in the database
        $checkEmailQuery = "SELECT * FROM registered_user WHERE EMAIL_USER = :EMAIL_USER";
        $checkEmailStmt = $db->prepare($checkEmailQuery);
        $checkEmailStmt->bindParam(':EMAIL_USER', $EMAIL_USER);
        $checkEmailStmt->execute();
    
        if ($checkEmailStmt->rowCount() > 0) {
            echo 'There is already a registered user with this email';
        } else {
            // Check if the user selected the "Administrator" role
            if ($ROLE === 'admin') {
                $adminKey = $_POST['admin-key'];

                // Check if the admin key is correct
                if ($adminKey !== 'ucaests') {
                    echo 'Incorrect admin key';
                    exit();
                }
            }

            // Upload and assign profile picture
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $targetDir = 'profiles_pictures/';
                $targetFile = $targetDir . basename($_FILES['profile_picture']['name']);
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

                // Check if the uploaded file is an image
                $check = getimagesize($_FILES['profile_picture']['tmp_name']);
                if ($check === false) {
                    echo 'Error: Invalid image file.';
                    exit();
                }

                // Move the uploaded file to the target directory
                if (!move_uploaded_file($_FILES['profile_picture']['tmp_name'], $targetFile)) {
                    echo 'Error: Failed to upload the profile picture.';
                    exit();
                }

                $profile_picture = $targetFile;
            } else {
                // No profile picture uploaded, assign a default value or handle it as per your requirement
                $profile_picture = './profile_pictures/profil.jpg'; // Assign a default value or handle it as per your requirement
            }

            // Insert new user
            $sql = "INSERT INTO registered_user (NAME_USER, LASTNAME_USER, USERNAME_USER, PASSWORD_USER, EMAIL_USER, ROLE, profile_picture) 
                    VALUES (:NAME_USER, :LASTNAME_USER, :USERNAME_USER, :PASSWORD_USER, :EMAIL_USER, :ROLE, :profile_picture)";

            // Prepare the SQL statement
            $stmt = $db->prepare($sql);

            $stmt->bindParam(':NAME_USER', $NAME_USER);
            $stmt->bindParam(':LASTNAME_USER', $LASTNAME_USER);
            $stmt->bindParam(':USERNAME_USER', $USERNAME_USER);
            $stmt->bindParam(':PASSWORD_USER', $PASSWORD_USER);
            $stmt->bindParam(':EMAIL_USER', $EMAIL_USER);
            $stmt->bindParam(':ROLE', $ROLE);
            $stmt->bindParam(':profile_picture', $profile_picture);

            // Execute the statement
            $stmt->execute();

            // Redirect to the login page or a success page
            header("Location: login.php");
            exit();
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
	<title>Sign Up</title>
	<link rel="stylesheet" href="UIStyle.css">
    <header style="background-color:black;">
        <img class="logo" src="images/OnTheDot.png" alt="Logo">
    </header>
    
</head>
<body>
    <style>
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
</form>
<body style="background-color:#EDEDED;">

    <main >
        <div style="margin-top:0px;margin-right:150px;" class="container">   
            <form class="login-form" style="box-shadow:12px 0px 60px 0px rgba(0,0,0,0.5);width:inherit;" action="Sign Up.php" method="POST">
			<h1 style="text-align:center;">Sign Up</h1>
            
            <div class="form-group">
				<label for="NAME_USER"><strong>Name</strong></label>
				<input type="text" id="NAME_USER" name="NAME_USER" required>
			</div>
            <div class="form-group">
				<label for="LASTNAME_USER"><strong>Last Name</strong></label>
				<input type="text" id="LASTNAME_USER" name="LASTNAME_USER" required>
			</div>
            <div class="form-group">
				<label for="EMAIL_USER"><strong>E-mail</strong></label>
				<input type="text" id="EMAIL_USER" name="EMAIL_USER" required>
			</div>
			<div class="form-group">
				<label for="USERNAME_USER"><strong>Username</strong></label>
				<input type="text" id="USERNAME_USER" name="USERNAME_USER" required>
			</div>
			<div class="form-group">
				<label for="PASSWORD_USER"><strong>Password</strong></label>
				<input type="password" id="PASSWORD_USER" name="PASSWORD_USER" required>
			</div>

			<div class="form-group">
            <div class="custom-radio">
                <input type="radio" id="admin" name="ROLE" value="admin" required>
                <label for="admin">Are You An Adminstrator?<label style="color:red;">*REQUIRES KEY</label></label>
                <span class="checkmark"></span>
            </div>

            <div class="custom-radio">
                <input type="radio" id="user" name="ROLE" value="user" required>
                <label for="user">Sign As a User</label>
                <span class="checkmark"></span>
            </div>
        </div>
        <div id="key-input">
            <div class="form-group">
                <label for="admin-key"><strong>Admin Key</strong></label>
                <input type="password" id="admin-key" name="admin-key">
            </div>
        </div>
            
			<button type="submit">Sign Up</button><br>
            <div class="line-hor" style="width:460px;"></div>
            <div class="antimsp">
            <label style="font-size:16px;positon:relative;"><strong>Already Have An Account? </strong><a href="login.php"><strong><i>Login</i></strong></a>
            </div>
		</form>
        </div>
        <div style="background-color: inherit; margin-left: 100px;">
            <br>
            <div class="slideshow-container">
                <div class="image-container">
                    <img class="hover-scale fade" style="width: 100%;" src="images/OnTheDate3.png" alt="Slideshow Image">
                </div>
            </div>
        </div>
    <main>
    
</body>
</html>