<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offers</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">    
</head>
<body>
    
<?php 
include "elements/header.php";
include "includes/dbh.inc.php";

// Check if the request is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If it's a bookmark request
    if (isset($_POST['room_id'])) {
        $roomId = $_POST['room_id'];
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Save the bookmark to the database (assuming you have a table named bookmarks)
        $stmt = $pdo->prepare("INSERT INTO bookmarks (user_id, room_id) VALUES (?, ?)");
        $stmt->execute([$userId, $roomId]);
    }
    // If it's a display bookmarked rooms request
    elseif (isset($_POST['display_bookmarks'])) {
        // Retrieve bookmarked room IDs for the current user
        $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        $stmt = $pdo->prepare("SELECT room_id FROM bookmarks WHERE user_id = ?");
        $stmt->execute([$userId]);
        $bookmarkedRooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Display room details for each bookmarked room
        foreach ($bookmarkedRooms as $roomId) {
            // Retrieve room details using $roomId and display them
            // Example: Query the rooms table to get room details
        }
    }
}
?>


<script>

function bookmarkRoom(roomId) {
    // Send AJAX request to save the bookmark
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "bookmark.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            // Handle response if needed
        }
    };
    xhr.send("room_id=" + roomId);
}


</script>