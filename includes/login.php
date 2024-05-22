<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameOrEmail = $_POST["username"]; 
    $pwd = $_POST["pwd"]; // Use a different variable name password conflicting with password in include "dbh.inc.php";

    try {
        include "dbh.inc.php";
        
        // Query to fetch user data based on username or email
        $query = "SELECT * FROM users WHERE (email = ? OR user = ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]); 
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($pwd, $user['password'])) { 
            // User found and password is correct
            //include "config_session.inc.php";
            session_start();
            $_SESSION["email"] = $user["email"]; 
            $_SESSION["username"] = $user["user"];
            $_SESSION["gender"] = $user["gender"]; 
            $_SESSION["account"] = $user["account"]; 
            $_SESSION["Id"] = $user["Id"];

            header("Location: ../MainPage.php");
            die();
        } elseif (!$user) {
            // No user found
            echo "User not found. Please check your username/email.";
        } else {
            // Password incorrect
            echo "Invalid password. Please try again.";
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
} else {
    // If the request method is not POST, redirect to MainPage.html
    header("Location: ../MainPage.php");
    exit();
}
?>
