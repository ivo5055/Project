<?php
session_start();
include 'db_connect.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $items = $data['items'] ?? [];

    if (!isset($_SESSION['account']) || empty($items)) {
        echo json_encode(['success' => false]);
        exit;
    }

    try {
        $sql = "INSERT INTO reserved_items (item_id, user_name) VALUES ";
        $values = [];
        foreach ($items as $itemId) {
            $values[] = "($itemId, '" . $_SESSION["username"] . "')";
        }
        $sql .= implode(', ', $values);

        $pdo->exec($sql);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}
?>
