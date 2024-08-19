<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Вход</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<?php include 'elements/header.php'; ?>

<div class="login-container"> 
    <h1 data-translate="true">Вход</h1>
    <div class="login-form">
        <form action="includes/login.php" method="post">
            <label for="username" data-translate="true">Потребителско име или имейл:</label>
            <input type="text" id="username" name="username" required>
            <span id="username-error" style="color: red;"></span><br><br>

            <label for="pwd" data-translate="true">Парола:</label>
            <input type="password" id="pwd" name="pwd" required>
            <span id="password-error" style="color: red;"></span><br><br>

            <input type="checkbox" id="remember" name="remember">
            <label for="remember" data-translate="true">Запомни ме</label><br><br>

            <a href="forgot_username.php" data-translate="true">Забравено потребителско име</a><br>
            <a href="forgot_password.php" data-translate="true">Забравена парола</a><br>
            <a href="Register.php" action="register.php" data-translate="true">Регистрация</a><br><br>
            <button type="submit" data-translate="true">Вход</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const serverErrors = {
            username: urlParams.get('username'),
            password: urlParams.get('password')
        };

        if (serverErrors.username) {
            document.getElementById('username-error').innerHTML = serverErrors.username;
        }
        if (serverErrors.password) {
            document.getElementById('password-error').innerHTML = serverErrors.password;
        }
    });
</script>

</body>
</html>
