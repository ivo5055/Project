<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['items']) && is_array($data['items']) && !empty($data['items'])) {
        $username = $_SESSION['username'];

        try {
            // Database connection
            $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Begin transaction
            $pdo->beginTransaction();

            // Prepare SQL statement
            $sql = "INSERT INTO reserved_items (item_id, user_name) VALUES (:item_id, :user_name)";
            $stmt = $pdo->prepare($sql);

            // Execute insertion for each item
            foreach ($data['items'] as $item) {
                if (isset($item['id']) && is_numeric($item['id'])) {
                    $itemId = htmlspecialchars($item['id']);
                    $stmt->execute([
                        ':item_id' => $itemId,
                        ':user_name' => $username
                    ]);
                } else {
                    // Log invalid item data for debugging
                    error_log("Invalid item data: " . print_r($item, true));
                }
            }

            // Commit transaction
            $pdo->commit();

            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            // Roll back transaction on error
            $pdo->rollBack();
            error_log("Database error: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No valid items provided']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>
