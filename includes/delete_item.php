<?php
include "dbh.inc.php"; 

// Check if the ID is provided
if (!isset($_GET['Id']) || empty($_GET['Id'])) {
    die('Item ID is missing.');
}

$itemId = intval($_GET['Id']); // Sanitize item ID

// Handle item deletion
try {
    // Prepare and execute SQL query for deletion
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE Id = ?");
    $stmt->execute([$itemId]);

    // Redirect to the previous page (or any page you prefer)
    header('Location: ../diner.php'); 
    exit;
} catch (PDOException $e) {
    die("Error: " . htmlspecialchars($e->getMessage()));
}
?>
