<?php
session_start();
include 'includes/dbh.inc.php';

if (!isset($_SESSION['Id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['Id'];

$query = "SELECT room.* FROM room 
          JOIN bookmarks ON room.Id = bookmarks.room_id 
          WHERE bookmarks.user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute([':user_id' => $userId]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmarked Rooms</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>

<?php include "elements/header.php"; ?>

<div class="header-container">
    <h1>Bookmarks</h1>
    <div class="room-offers">
        <?php
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            include "includes/offersDisplay.php";
        }
        ?>
    </div>
</div>

</body>
</html>
