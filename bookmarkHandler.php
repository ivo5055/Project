<?php
session_start();
include 'includes/dbh.inc.php';

header('Content-Type: application/json');

// Handle POST request for bookmarking
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $offerId = $input['offerId'];

    if (isset($_SESSION['Id']) && !empty($offerId)) {
        $userId = $_SESSION['Id'];

        // Check if the room is already bookmarked
        $checkQuery = "SELECT * FROM bookmarks WHERE user_id = :user_id AND room_id = :room_id";
        $checkStmt = $pdo->prepare($checkQuery);
        $checkStmt->execute(['user_id' => $userId, 'room_id' => $offerId]);

        if ($checkStmt->rowCount() > 0) {
            // Remove the bookmark if it already exists
            $deleteQuery = "DELETE FROM bookmarks WHERE user_id = :user_id AND room_id = :room_id";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->execute(['user_id' => $userId, 'room_id' => $offerId]);

            echo json_encode(['success' => true, 'message' => 'Bookmark removed.']);
        } else {
            // Add the bookmark
            $insertQuery = "INSERT INTO bookmarks (user_id, room_id) VALUES (:user_id, :room_id)";
            $insertStmt = $pdo->prepare($insertQuery);
            $insertStmt->execute(['user_id' => $userId, 'room_id' => $offerId]);

            echo json_encode(['success' => true, 'message' => 'Bookmark added.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user or offer ID.']);
    }
    exit();
}
?>
