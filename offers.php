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
?>

<div class="">
    <?php include "includes/show_booked.php"; ?>

    <h1>Room Offers</h1>
    <div class="room-offers">

    <?php include "includes/filter.php";?>
    
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

                //shows rooms that have space for guests and users
                //for admin it shows every room
                if ($currentBookings < $row['room_capacity'] || isset($_SESSION['account']) == "A") { 
                    if(isset($_SESSION['gender'])){
                        if($row['gender_R'] == $_SESSION['gender'] || $_SESSION['account'] == "A") include "includes/offersDisplay.php" ;
                    }
                    else if (!isset($_SESSION['gender'])|| $_SESSION['account'] == "U"){
                    include "includes/offersDisplay.php";
                }
            }}
            ?>
    </div>
</div>
</body>
</html>
