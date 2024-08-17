<?php
session_start();
header('Content-Type: application/json'); // Set content type to JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['items']) && !empty($data['items'])) {
        $username = $_SESSION['username'] ?? 'Guest';

        try {
            // Database connection
            $pdo = new PDO('mysql:host=localhost;dbname=your_database', 'username', 'password');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            foreach ($data['items'] as $item) {
                $itemId = htmlspecialchars($item['id']);
                $sql = "INSERT INTO reserved_items (item_id, user_name) VALUES (:item_id, :user_name)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':item_id' => $itemId,
                    ':user_name' => $username
                ]);
            }
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No items provided']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
