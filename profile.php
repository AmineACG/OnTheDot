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
        // Filter articles if a category is selected
        $userEmail = $_SESSION['EMAIL_USER'];
        $sql = "SELECT * FROM registered_user WHERE EMAIL_USER = :EMAIL_USER";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':EMAIL_USER', $userEmail);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $ROLE = $result['role'];
        $userEmail = $_SESSION['EMAIL_USER'];
      

        // Retrieve the admin information from the database
        $sql = "SELECT * FROM registered_user WHERE role = 'admin'";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="UIStyle.css">
    <style>
      .greeting {
        font-size: 24px;
        color: #333;
        margin-bottom: 20px;
        text-align: center;
    }

    .greeting span {
        display: block;
        margin-bottom: 5px;
    }

    .greeting .role {
        font-style: italic;
        color: #888;
        font-size: 18px;
    }
    </style>
    <div class="header">
        <a href="home.php"><img class="logo" src="images/OnTheDot.png" alt=""><img></a>
        
    </div>
</head>
<style>
    .table {
        width: 80%;
        margin: 20px auto;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
        
    th,
    td {
        padding: 50px;
        text-align: left;
    }
        
    th {
        background-color: #333;
        color: #fff;
    }
        
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
        
    tr:hover {
        background-color: #ddd;
    }
        
    a {
        color: #333;
        text-decoration: none;
    }
        
    a:hover {
        text-decoration: underline;
    }
    
    .file-upload-btn {
        display: inline-block;
        position: relative;
        overflow: hidden;
        padding: 0;
        margin-top: 10px;
        width: 150px; /* Adjust the width as needed */
        height: 150px; /* Adjust the height as needed */
        border-radius: 50%;
        background-color: #ccc;
        color: #fff;
        text-align: center;
        cursor: pointer;
        background-color: transparent;
        border: none;
        outline: none;
    }
    
    .file-upload-btn input[type="file"] {
        opacity: 0;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }
    
    .file-upload-btn img {
        border: 5px solid lightgreen;
        background-color: black;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    
    .button-container {
        text-align: center;
        margin-top: 20px;
    }
    
    .button-container a {
        display: inline-block;
        margin-right: 10px;
        background-color: #333;
        color: #fff;
        padding: 10px 20px;
        text-decoration: none;
    }
    
    .button-container a:last-child {
        margin-right: 0;
    }
</style>

<body> 
    <form style="background-color:white;padding-top:0px;" action="update_profile.php" method="POST">
        <fieldset style="background-color:white;">
            <legend>
                <label for="profile_picture" class="file-upload-btn"> 
                    <img style="object-fit:contain;" src="<?php echo $result['profile_picture']?>" alt="">
                </label>
            </legend>
            <p>Status : Online</p>
            <?php echo $result['USERNAME_USER']?>
            <p>User-Id: <?php echo "$result[ID_USER]";?></p>
          <input style="float:right;" type="submit" value="Edit Profile Picture">
            <br><br><div class="button-container">
            <?php if ($ROLE === 'admin'):?>
        <a href="myArticles.php">My Articles</a>
        <?php endif; ?>
        <a href="logout.php">Log Out</a>
    </div><br>
            <?php if ($ROLE === 'admin'):?>
                <h2>Admin List</h2>
                <table class="table">
                    <tr>
                        <th>Admin ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                    
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?php echo $admin['ID_USER']; ?></td>
                            <td><?php echo $admin['USERNAME_USER']; ?></td>
                            <td><?php echo $admin['EMAIL_USER']; ?></td>
                            <td><a href="delete_admin.php?id=<?php echo $admin['ID_USER']; ?>">Delete</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </fieldset>
    </form>

    
</body>
</html>
