<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
    
<?php include 'elements/header.php'; ?>

<div class="register-container">
    <h1>Register</h1>
    <div class="register-form">
        <form id="register-form" action="includes/register.php" method="post" onsubmit="return validateForm()">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="username">User Name:</label>
            <input type="text" id="user" name="user" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="pwd" name="pwd" required><br><br>

            <label for="confirm-password">Confirm Password:</label>
            <input type="password" id="confirm-password" name="confirm-password" required><br>
            <span id="password-match" style="color: red;"></span><br>

            <label for="gender">Gender:</label><br>
            <input type="radio" id="male" name="gender" value="male" checked>
            <label for="male">Male</label>
            <input type="radio" id="female" name="gender" value="female">
            <label for="female">Female</label><br><br>

            <input type="checkbox" id="agreement" name="agreement" required>
            <label for="agreement">I agree to the <a href="PRAVILNIK_NAST_final_2022.pdf" target="_blank">terms and conditions</a></label><br><br>

            <button type="submit">Create Profile</button><br><br>
            <p>Already have an existing account? <a href="Login.php">Login</a></p>
        </form>
    </div>
</div>



    <script>
        function validateForm() {
            var password = document.getElementById("pwd").value;
            var confirmPassword = document.getElementById("confirm-password").value;

            if (password !== confirmPassword) {
                document.getElementById("password-match").innerHTML = "Passwords do not match!";
                return false; // Prevent form submission
            } else {
                document.getElementById("password-match").innerHTML = "";
                return true; // Allow form submission
            }
        }
    </script>
</body>
</html>
