<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $user = $_POST["user"];
    $pwd = $_POST["pwd"];
    $gender = $_POST["gender"];

    try {
        include "dbh.inc.php";

        // Check if email already exists
        $queryEmail = "SELECT * FROM users WHERE email = ?";
        $stmtEmail = $pdo->prepare($queryEmail);
        $stmtEmail->execute([$email]);
        if ($stmtEmail->rowCount() > 0) {
            die("Email already registered. Please choose a different email.");
        }

        // Check if username already exists
        $queryUser = "SELECT * FROM users WHERE user = ?";
        $stmtUser = $pdo->prepare($queryUser);
        $stmtUser->execute([$user]);
        if ($stmtUser->rowCount() > 0) {
            die("Username already taken. Please choose a different username.");
        }

        // Hash the password
        $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);

        // Insert new user into database with hashed password
        $queryInsert = "INSERT INTO users (email, user, password, gender, account) VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $pdo->prepare($queryInsert);
        $stmtInsert->execute([$email, $user, $hashedPassword, $gender, "U"]);

        // Redirect after successful registration
        header("Location: ../MainPage.php");
        exit();
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    header("Location: ../MainPage.php");
    die();
}
?>
