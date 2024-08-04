<?php

// Handle reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserve'])) {
    $itemId = $_POST['item_id'] ?? '';
    $userName = $_POST['user_name'] ?? $_SESSION["username"]; // Get username from session

    // Sanitize inputs
    $itemId = htmlspecialchars($itemId);
    $userName = htmlspecialchars($userName);

    // Prepare and execute SQL query for insertion
    $sql = "INSERT INTO reserved_items (item_id, user_name) VALUES (:item_id, :user_name)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([
            ':item_id' => $itemId,
            ':user_name' => $userName
        ]);
    } catch (PDOException $e) {
        echo "Error: " . htmlspecialchars($e->getMessage());
    }
    header("Location: diner.php");
}
?>
