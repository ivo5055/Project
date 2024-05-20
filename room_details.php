<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Details</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dropdown.css">
</head>
<body>
<?php include 'elements/header.php'; ?>

<div class="room-details">
    <h1>Room Details</h1>
    <?php
        // Include database connection file
        include "includes/dbh.inc.php";

        // Check if room_number is provided in the URL
        if (isset($_GET['room_number'])) {
            $room_number = $_GET['room_number'];

            // Fetch room details from database
            $query = "SELECT * FROM room WHERE room_number = :room_number";
            $stmt = $pdo->prepare($query);
            $stmt->execute(['room_number' => $room_number]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($room) {
                $averageRating = $room['number_of_reviews'] > 0 ? round($room['total_rating'] / $room['number_of_reviews'], 1) : 0;
                echo '<div class="room-detail">';
                echo '<img src="img/' . $room['image_url'] . '">';
                echo '<p>Room number: ' . $room['room_number'] . '</p>';
                echo '<p>Room capacity: ' . $room['room_capacity'] . '</p>';
                echo '<p>' . $room['description'] . '</p>';
                echo '<p>Rating: ' . $averageRating . '/5 (' . $room['number_of_reviews'] . ' reviews)</p>';
                echo '<p>Price: $' . $room['price'] . ' per month</p>';
                
                // Check if user is logged in
                if (isset($_SESSION['username'])) {
                    $username = $_SESSION['username'];

                    // Check if user already booked a room
                    $checkBookingQuery = "SELECT COUNT(*) FROM bookings WHERE userN = :username";
                    $checkBookingStmt = $pdo->prepare($checkBookingQuery);
                    $checkBookingStmt->execute(['username' => $username]);
                    $userBookingCount = $checkBookingStmt->fetchColumn();

                    if ($userBookingCount > 0) {
                        echo '<p>You have already booked a room. You can only book one room at a time.</p>';
                    } else {
                        
                        // Check current bookings for this room
                        $checkRoomBookingQuery = "SELECT COUNT(*) FROM bookings WHERE room_number = :room_number";
                        $checkRoomBookingStmt = $pdo->prepare($checkRoomBookingQuery);
                        $checkRoomBookingStmt->execute(['room_number' => $room_number]);
                        $currentRoomBookings = $checkRoomBookingStmt->fetchColumn();

                        if ($currentRoomBookings < $room['room_capacity']) {
                            if (isset($_POST['book_room'])) {
                                // Book the room
                                $bookingQuery = "INSERT INTO bookings (userN, room_number) VALUES (:userN, :room_number)";
                                $bookingStmt = $pdo->prepare($bookingQuery);
                                $bookingStmt->execute(['userN' => $username, 'room_number' => $room_number]);

                                echo '<p>Room booked successfully!</p>';
                            } else {
                                echo '<form method="post" action="">';
                                echo '<button type="submit" name="book_room" class="button">Book Now</button>';
                                echo '</form>';
                            }
                        } else {
                            echo '<p>This room is fully booked. Please choose another room.</p>';
                        }
                    }
                } else {
                    echo '<p>Please <a href="login.php">login</a> to book this room.</p>';
                }

                echo '</div>';
            } else {
                echo '<p>Room not found.</p>';
            }
        } else {
            echo '<p>No room number provided.</p>';
        }
    ?>
</div>

</body>
</html>
