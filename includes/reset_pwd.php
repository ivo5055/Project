<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $pdw = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);
    $errors = [];

    // Validate password length
    if (strlen($pdw) < 8) {
        $errors['password'] = "Паролата трябва да бъде поне 8 символа!";
    }

    // Validate password confirmation
    if ($pdw !== $confirmPassword) {
        $errors['password'] = "Паролите не съвпадат.";
    }

    // Check if there are any errors
    if (empty($errors)) {
        try {
            include "dbh.inc.php";
            
            // Find the user by the reset token
            $query = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiry > NOW()";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Hash the new password
                $hashedPassword = password_hash($pdw, PASSWORD_DEFAULT);
                
                // Update the user's password and clear the token
                $query = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE Id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$hashedPassword, $user['Id']]);

                header("Location: ../Login.php?password_reset=1");
                exit();
            } else {
                $errors['token'] = "Невалиден или изтекъл токен.";
            }
        } catch (PDOException $e) {
            $errors['db'] = "Database error: " . $e->getMessage();
        }
    }

    // If there are validation errors, redirect with query parameters
    if (!empty($errors)) {
        $queryString = http_build_query($errors);
        header("Location: ../reset_password.php?token=" . urlencode($token) . "&" . $queryString);
        exit();
    }
} else {
    header("Location: ../MainPage.php");
    exit();
}
?>
