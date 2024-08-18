<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Регистрация</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
    
<?php include 'elements/header.php'; ?>

<div class="register-container">
    <h1 data-translate="true">Регистрация</h1>
    <div class="register-form">
        <form id="register-form" action="includes/register.php" method="post" onsubmit="return validateForm()">
            <label for="email" data-translate="true">Имейл:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="username" data-translate="true">Потребителско име:</label>
            <input type="text" id="user" name="user" required><br><br>

            <label for="password" data-translate="true">Парола:</label>
            <input type="password" id="pwd" name="pwd" required><br><br>

            <label for="confirm-password" data-translate="true">Потвърдете паролата:</label>
            <input type="password" id="confirm-password" name="confirm-password" required><br>
            <span id="password-match" style="color: red;"></span><br>

            <label for="gender" data-translate="true">Пол:</label><br>
            <input type="radio" id="male" name="gender" value="male" checked>
            <label for="male" data-translate="true">Мъж</label>
            <input type="radio" id="female" name="gender" value="female">
            <label for="female" data-translate="true">Жена</label><br><br>

            <input type="checkbox" id="agreement" name="agreement" required>
            <label for="agreement" data-translate="true">Съгласен съм с <a href="PRAVILNIK_NAST_final_2022.pdf" target="_blank" data-translate="true">условията и правилата</a></label><br><br>

            <button type="submit" data-translate="true">Създайте профил</button><br><br>
            <p data-translate="true">Вече имате съществуващ акаунт? <a href="Login.php" data-translate="true">Вход</a></p>
        </form>
    </div>
</div>

<script>
    function validateForm() {
        var password = document.getElementById("pwd").value;
        var confirmPassword = document.getElementById("confirm-password").value;

        if (password !== confirmPassword) {
            document.getElementById("password-match").innerHTML = "Паролите не съвпадат!";
            return false; // Prevent form submission
        } else {
            document.getElementById("password-match").innerHTML = "";
            return true; // Allow form submission
        }
    }
</script>
</body>
</html>
