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
    
<?php include "elements/header.php";
include "includes/dbh.inc.php"; 
?>

<div class="">
    <?php include "includes/show_booked.php"; ?>

    <h1>Room Offers</h1>
    <div class="room-offers">

    <?php include "includes/filter.php";?>
    
    <?php
            // Delete button
            include "includes/deleteOffer.php";

           

            // Loop through fetched room offers and generate HTML dynamically
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                // Count current bookings for this room
                $bookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number";
                $bookingStmt = $pdo->prepare($bookingQuery);
                $bookingStmt->execute(['room_number' => $row['room_number']]);
                $currentBookings = $bookingStmt->fetchColumn();

                if ($currentBookings < $row['room_capacity']) {
                    echo '<div class="room-offer">';
                    echo '<a href="room_details.php?room_number=' . $row['room_number'] . '">';
                    echo '</a>';
                    echo '<div class="offer-details">';
                    echo '<img src="img/' . $row['image_url'] . '">'; // Corrected image source
                    echo '<p>Room number: ' . $row['room_number'] . '</p>';
                    // Show how many rooms are booked
                    echo '<p>Booked: ' . $currentBookings . ' / ' . $row['room_capacity'] . '</p>';
                    // Calculate and display the average rating and number of reviews
                    $averageRating = $row['number_of_reviews'] > 0 ? round($row['total_rating'] / $row['number_of_reviews'], 1) : 0;
                    echo '<p>Rating: ' . $averageRating . '/5 (' . $row['number_of_reviews'] . ' reviews)</p>';
                    echo '<p>Price: $' . $row['price'] . ' per month</p>';

                    // Link to room details
                    echo '<p> <a href="room_details.php?room_number=' . $row['room_number'] . '" class="button">Book Now</a> </p>';

                    // Admin delete button
                    if (isset($_SESSION['account']) && $_SESSION['account'] == 'A') {
                        echo '<form method="post" action="">';
                        echo '<input type="hidden" name="Id" value="' . $row['Id'] . '">'; // Hidden input to send room ID
                        echo '<button type="submit" name="delete_room" class="button">Delete</button>'; // Delete button
                        echo '</form>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
    </div>
</div>
</body>
</html>
