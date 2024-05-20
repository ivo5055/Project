<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
    
<?php include 'elements/header.php'; ?>

    <div class="">
        <h1>Login</h1>
        <div class="login-form">
            <form action="includes/login.php" method="post">
                <label for="username">Username or Email:</label>
                <input type="text" id="username" name="username" required><br><br>

                <label for="pwd">Password:</label>
                <input type="password" id="pwd" name="pwd" required><br><br>

                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label><br><br>

                <a href="forgot_username.php">Forgotten Username</a><br>
                <a href="forgot_password.php">Forgotten Password</a><br>
                <a href="Register.php" action="register.php">Register</a><br><br>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
