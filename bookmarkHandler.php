<?php
session_start();
include "includes/dbh.inc.php";

$userId = isset($_SESSION['Id']) ? $_SESSION['Id'] : null;
$input = json_decode(file_get_contents('php://input'), true);
$offerId = isset($input['offerId']) ? $input['offerId'] : null;
$action = isset($input['action']) ? $input['action'] : null;

if ($userId && $offerId && in_array($action, ['add', 'remove'])) {
    if ($action == 'add') {
        $stmt = $pdo->prepare("INSERT INTO bookmarks (user_id, room_id) VALUES (?, ?)");
        $success = $stmt->execute([$userId, $offerId]);
    } else {
        $stmt = $pdo->prepare("DELETE FROM bookmarks WHERE user_id = ? AND room_id = ?");
        $success = $stmt->execute([$userId, $offerId]);
    }

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
