<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = trim($_POST["username"]);
    $pwd = $_POST["pwd"];
    $errors = [];

    try {
        include "dbh.inc.php";
        
        $query = "SELECT * FROM users WHERE (email = ? OR user = ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]); 
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pwd, $user['password'])) {
            session_start();
            $_SESSION["email"] = $user["email"];
            $_SESSION["username"] = $user["user"];
            $_SESSION["gender"] = $user["gender"];
            $_SESSION["account"] = $user["account"];
            $_SESSION["Id"] = $user["Id"];

            header("Location: ../MainPage.php");
            exit();
        } elseif (!$user) {
            $errors['username'] = "Потребителят не е намерен. Моля, проверете вашето потребителско име/имейл.";
        } else {
            $errors['password'] = "Невалидна парола. Моля, опитайте отново.";
        }

        if (!empty($errors)) {
            $queryString = http_build_query($errors);
            header("Location: ../Login.php?" . $queryString);
            exit();
        }
    } catch (PDOException $e) {
        $errors['db'] = "Database error: " . $e->getMessage();
        $queryString = http_build_query($errors);
        header("Location: ../Login.php?" . $queryString);
        exit();
    }
} else {
    header("Location: ../MainPage.php");
    exit();
}
?>
