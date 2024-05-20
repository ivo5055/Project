<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
   
<?php include 'elements/header.php'; ?>

<div class="container">
    <div class="content">
        <h1>Информационно табло за студенти</h1>
        <p>С нашата платформа можете лесно и бързо да кандидатствате за общежитие онлайн, като пестоте време и усилия.</p>
        
        <?php
        if (!isset($_SESSION['username'])) {
            // Display the Register button only if there is no active session
            echo '<a href="Register.php" class="register-button">Register</a>';
        }
        ?>

    </div>
    <div class="image-container">
        <img src="img/uni.jpg" alt="Description of the image">
    </div>
</div>

</body>
</html>
