<?php
include "includes/dbh.inc.php";

// Define variables for error messages
$emailError = $usernameError = $passwordError = "";

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    session_start(); // Make sure to start the session
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
    
    <form id="profileForm" method="post" class="hidden">
        <div>
            <label for="email">New Email:</label>
            <input type="text" name="email" placeholder="New Email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>">
        </div>
        <div>
            <label for="username">New Username:</label>
            <input type="text" name="username" placeholder="New Username" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>">
        </div>
        <div>
            <label for="password">New Password:</label>
            <input type="password" name="password" placeholder="New Password">
        </div>
        <div>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" placeholder="Confirm Password">
        </div>
        <?php if (!empty($passwordError)) echo "<p style='color:red;'>$passwordError</p>"; ?>
        <button type="submit">Apply</button>
    </form>
    
    <button id="editProfileButton">Edit Profile</button>
</div>

<script>
    document.getElementById('editProfileButton').addEventListener('click', function() {
        var profileForm = document.getElementById('profileForm');
        
        // Check if the 'profileForm' element has the class 'hidden'
        if (profileForm.classList.contains('hidden')) {
            // If 'profileForm' is hidden, remove the 'hidden' class to show it
            profileForm.classList.remove('hidden');
        } else {
            // If 'profileForm' is visible, add the 'hidden' class to hide it
            profileForm.classList.add('hidden');
        }
    });
</script>

    
</body>
</html>