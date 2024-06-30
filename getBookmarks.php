<?php
session_start();
include "includes/dbh.inc.php";

$userId = isset($_SESSION['Id']) ? $_SESSION['Id'] : null;

if ($userId) {
    $stmt = $pdo->prepare("SELECT room_id FROM bookmarks WHERE user_id = ?");
    $stmt->execute([$userId]);
    $bookmarks = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['success' => true, 'bookmarks' => $bookmarks]);
} else {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
}
?>
