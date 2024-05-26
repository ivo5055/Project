<?php
include "includes/dbh.inc.php";

if (isset($_GET['building'])) {
    $building = $_GET['building'];
    $stmt = $pdo->prepare("SELECT * FROM room WHERE building = :building");
    $stmt->execute(['building' => $building]);
    $roomOffers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($roomOffers);
    exit();
}
?>
