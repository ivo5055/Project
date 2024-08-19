<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $user = trim($_POST["user"]);
    $pwd = $_POST["pwd"];
    $gender = $_POST["gender"];
    $errors = [];

    try {
        include "dbh.inc.php";

        // Begin a transaction
        $pdo->beginTransaction();

        // Check if email already exists
        $queryEmail = "SELECT 1 FROM users WHERE email = ?";
        $stmtEmail = $pdo->prepare($queryEmail);
        $stmtEmail->execute([$email]);
        if ($stmtEmail->fetch()) {
            $errors['email'] = "Имейл адресът вече е регистриран. Моля, изберете друг имейл.";
        }

        // Check if username already exists
        $queryUser = "SELECT 1 FROM users WHERE user = ?";
        $stmtUser = $pdo->prepare($queryUser);
        $stmtUser->execute([$user]);
        if ($stmtUser->fetch()) {
            $errors['user'] = "Потребителското име вече е заето. Моля, изберете друго потребителско име.";
        }

        if (!empty($errors)) {
            $pdo->rollBack();
            $queryString = http_build_query($errors);
            header("Location: ../Register.php?" . $queryString);
            exit();
        }

        // Hash the password securely
        $hashedPassword = password_hash($pwd, PASSWORD_DEFAULT);

        // Insert new user into the database with hashed password
        $queryInsert = "INSERT INTO users (email, user, password, gender, account) VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $pdo->prepare($queryInsert);
        $stmtInsert->execute([$email, $user, $hashedPassword, $gender, "U"]);

        // Commit the transaction
        $pdo->commit();

        header("Location: ../MainPage.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $errors['db'] = "Database error: " . $e->getMessage();
        $queryString = http_build_query($errors);
        header("Location: ../Register.php?" . $queryString);
        exit();
    }
} else {
    header("Location: ../MainPage.php");
    exit();
}
?>
