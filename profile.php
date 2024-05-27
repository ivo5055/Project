<?php
session_start();
include "includes/dbh.inc.php";

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
                $emailError = "Email already registered. Please choose a different email.";
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
                $usernameError = "Username already taken. Please choose a different username.";
            } else {
                $queryUpdateUser = "UPDATE users SET user = ? WHERE Id = ?";
                $stmtUpdateUser = $pdo->prepare($queryUpdateUser);
                $stmtUpdateUser->execute([$newUsername, $userId]);
                $_SESSION['username'] = $newUsername; // Update session
            }
        }

        // Validate password and confirm password
        if ($newPassword !== $confirmPassword) {
            $passwordError = "Passwords do not match.";
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
$queryUserDetails = "SELECT full_name, fn, egn FROM users WHERE Id = ?";
$stmtUserDetails = $pdo->prepare($queryUserDetails);
$stmtUserDetails->execute([$userId]);
$userDetails = $stmtUserDetails->fetch(PDO::FETCH_ASSOC);

$egnLength = strlen($userDetails['egn']);
$maskedEgn = $egnLength > 2 ? substr($userDetails['egn'], 0, 2) . str_repeat('*', $egnLength - 2) : $userDetails['egn'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
    <style>
        .hidden { display: none; }
    </style>
</head>
<body>
    
<?php include 'elements/header.php'; ?>

<div>
    <h2>Account</h2>
    
    <p>Email: <?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Not set'; ?> 
    <?php if (!empty($emailError)) echo "<p style='color:red;'>$emailError</p>"; ?>
    
    <p>Username: <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Not set'; ?></p>
    <?php if (!empty($usernameError)) echo "<p style='color:red;'>$usernameError</p>"; ?>
    
    <p>Full Name: <?php echo htmlspecialchars($userDetails['full_name']); ?></p>
    <p>Faculty Number (FN): <?php echo htmlspecialchars($userDetails['fn']); ?></p>
    <p>EGN: <?php echo htmlspecialchars($maskedEgn); ?></p>
    
    <button id="editProfileButton">Edit Profile</button>
</div>

<script>
    document.getElementById('editProfileButton').addEventListener('click', function() {
        window.location.href = 'edit_profile.php';
    });
</script>
</body>
</html>
