<?php
session_start();
include "includes/dbh.inc.php";
if (!isset($_SESSION['account'])) {
    header("Location: MainPage.php");
    exit();
}
// Define variables for error messages
$emailError = $usernameError = $passwordError = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newEmail = $_POST['email'] ?? null;
    $newUsername = $_POST['username'] ?? null;
    $newPassword = $_POST['password'] ?? null; // New password
    $confirmPassword = $_POST['confirm_password'] ?? null; // Confirm password
    $userId = $_SESSION['Id'];

    // Update email, username, and password
    if ($newEmail || $newUsername || $newPassword) {
        // Update email
        if ($newEmail && $newEmail !== $_SESSION['email']) {
            $queryEmail = "SELECT * FROM users WHERE email = ?";
            $stmtEmail = $pdo->prepare($queryEmail);
            $stmtEmail->execute([$newEmail]);
            if ($stmtEmail->rowCount() > 0) {
                $emailError = "Имейлът вече е регистриран. Моля, изберете различен имейл.";
            } else {
                $queryUpdateEmail = "UPDATE users SET email = ? WHERE Id = ?";
                $stmtUpdateEmail = $pdo->prepare($queryUpdateEmail);
                $stmtUpdateEmail->execute([$newEmail, $userId]);
                $_SESSION['email'] = $newEmail; // Update session
            }
        }

        // Update username
        if ($newUsername && $newUsername !== $_SESSION['username']) {
            $queryUser = "SELECT * FROM users WHERE user = ?";
            $stmtUser = $pdo->prepare($queryUser);
            $stmtUser->execute([$newUsername]);
            if ($stmtUser->rowCount() > 0) {
                $usernameError = "Потребителското име вече е заето. Моля, изберете различно име.";
            } else {
                $queryUpdateUser = "UPDATE users SET user = ? WHERE Id = ?";
                $stmtUpdateUser = $pdo->prepare($queryUpdateUser);
                $stmtUpdateUser->execute([$newUsername, $userId]);
                $_SESSION['username'] = $newUsername; // Update session
            }
        }

        // Validate password and confirm password
        if ($newPassword !== $confirmPassword) {
            $passwordError = "Паролите не съвпадат.";
        } else {
            // Update password if confirmed
            if ($newPassword) {
                // You should include appropriate password hashing/salting here
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $queryUpdatePassword = "UPDATE users SET password = ? WHERE Id = ?";
                $stmtUpdatePassword = $pdo->prepare($queryUpdatePassword);
                $stmtUpdatePassword->execute([$hashedPassword, $userId]);
            }
        }
    }
}

$userId = $_SESSION['Id'];
$queryUserDetails = "SELECT full_name, fn, egn, gender, account FROM users WHERE Id = ?";
$stmtUserDetails = $pdo->prepare($queryUserDetails);
$stmtUserDetails->execute([$userId]);
$userDetails = $stmtUserDetails->fetch(PDO::FETCH_ASSOC);

$egnLength = strlen($userDetails['egn']);
$maskedEgn = $egnLength > 2 ? substr($userDetails['egn'], 0, 2) . str_repeat('*', $egnLength - 2) : $userDetails['egn'];
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Профил</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
    <link rel="stylesheet" href="profile.css"> <!-- Link to the new CSS file -->
</head>
<body>
    
<?php include 'elements/header.php'; ?>

<div class="profile-container">
    <h2 class="profile-header" data-translate="true">Детайли на акаунта</h2>
    
    <p class="profile-details"><span class="profile-label" data-translate="true">Имейл:</span> <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Не е зададен'; ?></p>
    <?php if (!empty($emailError)) echo "<p class='profile-error'>$emailError</p>"; ?>
    
    <p class="profile-details"><span class="profile-label" data-translate="true">Потребителско име:</span> <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Не е зададен'; ?></p>
    <?php if (!empty($usernameError)) echo "<p class='profile-error'>$usernameError</p>"; ?>
    
    <p class="profile-details"><span class="profile-label" data-translate="true">Пълно име:</span> <?php echo htmlspecialchars($userDetails['full_name']); ?></p>
    <p class="profile-details"><span class="profile-label" data-translate="true">Факултетен номер (FN):</span> <?php echo htmlspecialchars($userDetails['fn']); ?></p>
    <p class="profile-details"><span class="profile-label" data-translate="true">ЕГН:</span> <?php echo htmlspecialchars($maskedEgn); ?></p>
    <p class="profile-details"><span class="profile-label" data-translate="true">Пол:</span> <?php echo htmlspecialchars($userDetails['gender']); ?></p>
    <p class="profile-details"><span class="profile-label" data-translate="true">Тип акаунт:</span> <?php echo htmlspecialchars($userDetails['account']); ?></p>
    
    <button id="editProfileButton" class="profile-button" data-translate="true">Редактиране на профила</button>
</div>

<script>
    document.getElementById('editProfileButton').addEventListener('click', function() {
        window.location.href = 'edit_profile.php';
    });
</script>
</body>
</html>
