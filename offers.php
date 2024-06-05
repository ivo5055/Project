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

// Fetch the user's grade from the database
$grade = null;
if (isset($_SESSION['Id'])) {
    $userId = $_SESSION['Id'];
    $stmt = $pdo->prepare("
        SELECT sd.grade 
        FROM students_db sd
        JOIN users u ON u.fn = sd.fn
        WHERE u.Id = ?
    ");
    $stmt->execute([$userId]);
    $grade = $stmt->fetchColumn();
}
?>

<div class="header-container">

<?php include "includes/show_booked.php"; 
if (isset($_POST['room_id'])) {
$roomId = $_POST['room_id'];
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Save the bookmark to the database (assuming you have a table named bookmarks)
$stmt = $pdo->prepare("INSERT INTO bookmarks (user_id, room_id) VALUES (?, ?)");
$stmt->execute([$userId, $roomId]);
}
?>

    <h1><p></p>Room Offers<p></p></h1>
    
        <!-- Building Filter -->
        <div id="buildingFilterForm" class="radio-button-form">
            <button class="building-button active" data-building="1">Building 1</button>
            <button class="building-button" data-building="2">Building 2</button>
            <button class="building-button" data-building="3">Building 3</button>
            <button class="building-button" data-building="4">Building 4</button>
            <button class="building-button" data-building="5">Building 5</button>
            <button class="building-button" data-building="6" <?php if ($grade !== null && $grade < 5) echo 'disabled'; ?>>Building 6</button>
            <button id="filterButton" class="Filter_B">Filter</button>
        </div>

    <?php include "includes/filter.php";?>

    <div class="room-offers">
        <?php
        // Delete
        include "includes/deleteOffer.php";

        // Loop through fetched room offers and generate HTML dynamically
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            // Count current bookings for this room
            $bookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number";
            $bookingStmt = $pdo->prepare($bookingQuery);
            $bookingStmt->execute(['room_number' => $row['room_number']]);
            $currentBookings = $bookingStmt->fetchColumn();

            // Check if the building filter is set
            if (isset($_GET['building'])) {
                // Check if the room belongs to the selected building
                if ($_GET['building'] == $row['building']) {
                    // Check if the room is available or the user is an admin
                    if ($currentBookings < $row['room_capacity'] || (isset($_SESSION['account']) && $_SESSION['account'] == "A")) {
                        // Include room display
                        if (isset($_SESSION['gender']) && $_SESSION['account'] == "U") {
                            if ($row['gender_R'] == $_SESSION['gender']) {
                                include "includes/offersDisplay.php";
                            }
                        } else if (!isset($_SESSION['gender']) || $_SESSION['account'] == "A") {
                            include "includes/offersDisplay.php";
                        }
                    }
                }
            } else {
                // If building filter is not set, show rooms from Building 1
                if ($row['building'] == 1) {
                    // Check if the room is available or the user is an admin
                    if ($currentBookings < $row['room_capacity'] || (isset($_SESSION['account']) && $_SESSION['account'] == "A")) {
                        // Include room display
                        if (isset($_SESSION['gender']) && $_SESSION['account'] == "U") {
                            if ($row['gender_R'] == $_SESSION['gender']) {
                                include "includes/offersDisplay.php";
                            }
                        } else if (!isset($_SESSION['gender']) || $_SESSION['account'] == "A") {
                            include "includes/offersDisplay.php";
                        }
                    }
                }
            }
            
        }
        ?>
    </div>
</div>

<script src="js/building_filter.js"></script>
<script src="js/filter.js"></script>
<script src="js/bookmark.js"></script>

</body>
</html>
