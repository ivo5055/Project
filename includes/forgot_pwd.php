<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $errors = [];
    $successMessage = '';

    try {
        include "dbh.inc.php";

        // Check if email exists in the database
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Generate reset token and expiry time
            $token = bin2hex(random_bytes(32)); // Generate a secure random token
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token valid for 1 hour

            // Store the token and expiry time in the database
            $query = "UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE email = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$token, $expiry, $email]);

            // Send the reset email (example using mail() function)
            $resetLink = "http://localhost/Project/reset_password.php?token=" . urlencode($token);
            $subject = "Възстановяване на парола";
            $message = "<html><body>";
            $message .= "<p>Кликнете върху следния линк, за да възстановите вашата парола:</p>";
            $message .= "<p><a href=\"" . $resetLink . "\">" . $resetLink . "</a></p>";
            $message .= "</body></html>";
            $headers = "From: dorm@uni-ruse.bg\r\n";
            $headers .= "Reply-To: your-email@example.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n"; // Поддръжка на HTML формат

            if (mail($email, $subject, $message, $headers)) {
                $successMessage = "Имейлът за възстановяване на паролата е изпратен успешно.";
            } else {
                $errors['email'] = "Грешка при изпращане на имейла. Моля, опитайте отново.";
            }
        } else {
            $errors['email'] = "Имейл адресът не съществува.";
        }
    } catch (PDOException $e) {
        $errors['db'] = "Грешка в базата данни: " . $e->getMessage();
    }

    // Redirect with success or error message
    $queryString = http_build_query(array_merge($errors, ['success' => $successMessage]));
    header("Location: ../forgot_password.php?" . $queryString);
    exit();
} else {
    header("Location: ../MainPage.php");
    exit();
}
?>
