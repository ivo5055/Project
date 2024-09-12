<?php
session_start();
include "includes/dbh.inc.php";

if (!isset($_SESSION['account'])) {
    header("Location: MainPage.php");
    exit();
}
// Define variables for error messages
$emailError = $usernameError = $passwordError = $fnError = $egnError = "";

$userId = $_SESSION['Id'];
$queryUserDetails = "SELECT full_name, fn, egn, email, user FROM users WHERE Id = ?";
$stmtUserDetails = $pdo->prepare($queryUserDetails);
$stmtUserDetails->execute([$userId]);
$userDetails = $stmtUserDetails->fetch(PDO::FETCH_ASSOC);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newEmail = $_POST['email'] ?? null;
    $newUsername = $_POST['username'] ?? null;
    $newPassword = $_POST['password'] ?? null; // New password
    $confirmPassword = $_POST['confirm_password'] ?? null; // Confirm password
    $newFullName = $_POST['full_name'] ?? null;
    $newFn = $_POST['fn'] ?? null;
    $newEgn = $_POST['egn'] ?? null;

    // Update email, username, password, full name, fn, and egn
    if ($newEmail || $newUsername || $newPassword || $newFullName || $newFn || $newEgn) {
        // Update email
        if ($newEmail && $newEmail !== $userDetails['email']) {
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
        if ($newUsername && $newUsername !== $userDetails['user']) {
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
        if ($newPassword && $newPassword !== $confirmPassword) {
            $passwordError = "Паролите не съвпадат.";
        } elseif ($newPassword) {
            // Update password if confirmed
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $queryUpdatePassword = "UPDATE users SET password = ? WHERE Id = ?";
            $stmtUpdatePassword = $pdo->prepare($queryUpdatePassword);
            $stmtUpdatePassword->execute([$hashedPassword, $userId]);
        }

        // Update full name
        if ($newFullName && $newFullName !== $userDetails['full_name']) {
            $queryUpdateFullName = "UPDATE users SET full_name = ? WHERE Id = ?";
            $stmtUpdateFullName = $pdo->prepare($queryUpdateFullName);
            $stmtUpdateFullName->execute([$newFullName, $userId]);
        }

        // Check if FN and EGN are both provided or both are left unchanged
        if (($newFn && !$newEgn) || (!$newFn && $newEgn)) {
            if ($newFn && !$newEgn) {
                $fnError = "Моля, предоставете както FN, така и EGN.";
            } elseif (!$newFn && $newEgn) {
                $egnError = "Моля, предоставете както FN, така и EGN.";
            }
        } else {
            // Update faculty number (fn) and EGN if both are provided and if EGN has less than 10 characters
            if ($newFn && $newEgn && strlen($userDetails['egn']) < 10) {
                $queryFn = "SELECT * FROM students_db WHERE fn = ? AND egn = ?";
                $stmtFn = $pdo->prepare($queryFn);
                $stmtFn->execute([$newFn, $newEgn]);
                if ($stmtFn->rowCount() > 0) {
                    $queryUpdateFnEgn = "UPDATE users SET fn = ?, egn = ? WHERE Id = ?";
                    $stmtUpdateFnEgn = $pdo->prepare($queryUpdateFnEgn);
                    $stmtUpdateFnEgn->execute([$newFn, $newEgn, $userId]);
                } else {
                    $egnError = "Комбинацията от факултетен номер и ЕГН не съществува в базата данни на студентите.";
                }
            }
        }

        // Redirect back to profile page after updates if there are no errors
        if (empty($emailError) && empty($usernameError) && empty($passwordError) && empty($fnError) && empty($egnError)) {
            header("Location: profile.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title data-translate="true">Редактиране на профил</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
    <link rel="stylesheet" href="profile.css"> <!-- Link to the CSS file -->
</head>
<body>
    
<?php include 'elements/header.php'; ?>

<div class="edit-profile-page">
    <h2 class="edit-profile-header" data-translate="true">Редактиране на профил</h2>

    <form id="profileForm" method="post" class="edit-profile-form">
        <div>
            <label for="full_name" class="edit-profile-label" data-translate="true">Пълно име:</label>
            <input type="text" name="full_name" class="edit-profile-input" placeholder="Пълно име" value="<?php echo htmlspecialchars($userDetails['full_name']); ?>">
        </div>
        <div>
            <label for="email" class="edit-profile-label" data-translate="true">Нов имейл:</label>
            <input type="text" name="email" class="edit-profile-input" placeholder="Нов имейл" value="<?php echo htmlspecialchars($userDetails['email']); ?>">
            <?php if (!empty($emailError)) echo "<p class='edit-profile-error'>$emailError</p>"; ?>
        </div>
        <div>
            <label for="username" class="edit-profile-label" data-translate="true">Ново потребителско име:</label>
            <input type="text" name="username" class="edit-profile-input" placeholder="Ново потребителско име" value="<?php echo htmlspecialchars($userDetails['user']); ?>">
            <?php if (!empty($usernameError)) echo "<p class='edit-profile-error'>$usernameError</p>"; ?>
        </div>
        <div>
            <label for="password" class="edit-profile-label" data-translate="true">Нова парола:</label>
            <input type="password" name="password" class="edit-profile-input" placeholder="Нова парола">
        </div>
        <div>
            <label for="confirm_password" class="edit-profile-label" data-translate="true">Потвърдете паролата:</label>
            <input type="password" name="confirm_password" class="edit-profile-input" placeholder="Потвърдете паролата">
            <?php if (!empty($passwordError)) echo "<br><br><p class='edit-profile-error'>$passwordError</p>"; ?>
        </div>


        <?php if (strlen($userDetails['egn']) < 10): ?>
        <div>
            <label for="fn" class="edit-profile-label" data-translate="true">Факултетен номер (FN):</label>
            <input type="text" name="fn" class="edit-profile-input" placeholder="Факултетен номер" value="<?php echo htmlspecialchars($userDetails['fn']); ?>">
        </div>
        <div>
            <label for="egn" class="edit-profile-label" data-translate="true">ЕГН:</label>
            <input type="text" name="egn" class="edit-profile-input" placeholder="ЕГН" value="<?php echo htmlspecialchars($userDetails['egn']); ?>">
            <?php if (!empty($egnError)) echo "<p class='edit-profile-error'><br>$egnError</p>"; ?>
        </div>
        <?php endif; ?>
        
        <button type="submit" class="edit-profile-button" data-translate="true">Запази данните</button>
    </form>
</div>

</body>
</html>
