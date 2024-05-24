<?php

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    if (isset($_POST['cancel_booking'])) {
        $cancelBookingQuery = "DELETE FROM bookings WHERE userN = :username";
        $cancelBookingStmt = $pdo->prepare($cancelBookingQuery);
        $cancelBookingStmt->execute(['username' => $username]);

        // Clear rating session variables
        unset($_SESSION['has_rated']);
        unset($_SESSION['user_rating']);

        // Redirect to the same page to avoid form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Fetch the currently booked room for the logged-in user
    $currentBookingQuery = "SELECT * FROM room 
                            JOIN bookings ON room.room_number = bookings.room_number 
                            WHERE bookings.userN = :username";
    $currentBookingStmt = $pdo->prepare($currentBookingQuery);
    $currentBookingStmt->execute(['username' => $username]);
    $bookedRoom = $currentBookingStmt->fetch(PDO::FETCH_ASSOC);

    if ($bookedRoom) {
        echo '<div class="booked-room">';
        echo '<h1>Booked Room</h1>';
        echo '<div class="room-detail">';
        //echo '<img src="img/' . $bookedRoom['image_url'] . '">';
        echo '<p>Room number: ' . $bookedRoom['room_number'] . '</p>';
        echo '<p>Room capacity: ' . $bookedRoom['room_capacity'] . '</p>';

        // Rating system
        if (isset($_POST['rate_room'])) {
            $rating = $_POST['rating'];
            $room_number = $bookedRoom['room_number'];

            // Update total_rating and number_of_reviews in room table
            $updateRoomQuery = "UPDATE room
                                SET total_rating = total_rating + :rating, 
                                    number_of_reviews = number_of_reviews + 1
                                WHERE room_number = :room_number";
            $updateRoomStmt = $pdo->prepare($updateRoomQuery);
            $updateRoomStmt->execute(['rating' => $rating, 'room_number' => $room_number]);

            // Set session variable to indicate rating is done
            $_SESSION['has_rated'] = $room_number;
            $_SESSION['user_rating'] = $rating;

            // Redirect to the same page to avoid form resubmission
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

         // Fetch the updated rating
    $ratingQuery = "SELECT total_rating, number_of_reviews FROM room WHERE room_number = :room_number";
    $ratingStmt = $pdo->prepare($ratingQuery);
    $ratingStmt->execute(['room_number' => $bookedRoom['room_number']]);
    $ratingData = $ratingStmt->fetch(PDO::FETCH_ASSOC);

    $averageRating = $ratingData['number_of_reviews'] > 0 ? round($ratingData['total_rating'] / $ratingData['number_of_reviews'], 1) : 0;

    echo '<p>Rating: ' . $averageRating . '/5 (' . $ratingData['number_of_reviews'] . ' reviews)</p>';

    // Show star rating form if the user has not rated the room
    if (!isset($_SESSION['has_rated']) || $_SESSION['has_rated'] != $bookedRoom['room_number']) {
        echo '<form method="post" action="">';
        echo '<label for="rating">Rate this room:</label>';
        echo '<div class="rating">';
        for ($i = 5; $i >= 1; $i--) {
            echo '<input type="radio" id="star' . $i . '" name="rating" value="' . $i . '">';
            echo '<label for="star' . $i . '">☆</label>';
        }
        echo '</div>';
        echo '<button type="submit" name="rate_room" class="button">Submit Rating</button>';
        echo '</form>';
    } else {
        // Display the user's rating
        $userRating = $_SESSION['user_rating'];
        echo '<div class="rating">';
        for ($i = 5; $i >= 1; $i--) {
            if ($i == $userRating) {
                echo '<input type="radio" id="star' . $i . '" name="rating" value="' . $i . '" checked disabled>';
            } else {
                echo '<input type="radio" id="star' . $i . '" name="rating" value="' . $i . '" disabled>';
            }
            echo '<label for="star' . $i . '">☆</label>';
        }
        echo '</div>';
    }

    echo '<form method="post" action="">';
    echo '<p></p><button type="submit" name="cancel_booking" class="button">Cancel Booking</button>';
    echo '</form>';
    echo '</div>';
    echo '</div>';
    }
} 
?>
