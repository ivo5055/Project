<?php
include 'dbh.inc.php'; // Include your database connection file

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Automatically cancel expired bookings 1Month and 2Weeks advance AKA if date is - 15.03 would be expired at 1.02
    $currentDateTime = new DateTime();
    $expirationDateTime = (clone $currentDateTime)->sub(new DateInterval('P1M'))->sub(new DateInterval('P2W'));
    
    // Query to select expired bookings
    $selectExpiredBookingsQuery = "SELECT Id FROM bookings WHERE booking_date < :expiration_date AND userN = :username";
    $selectExpiredBookingsStmt = $pdo->prepare($selectExpiredBookingsQuery);
    $selectExpiredBookingsStmt->execute([
        'expiration_date' => $expirationDateTime->format('Y-m-d H:i:s'),
        'username' => $username
    ]);

    // Fetch expired bookings
    $expiredBookings = $selectExpiredBookingsStmt->fetchAll(PDO::FETCH_ASSOC);

    // Cancel expired bookings
    foreach ($expiredBookings as $booking) {
        $cancelBookingQuery = "DELETE FROM bookings WHERE Id = :Id";
        $cancelBookingStmt = $pdo->prepare($cancelBookingQuery);
        $cancelBookingStmt->execute(['Id' => $booking['Id']]);
    }

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

    if (isset($_POST['pay_next_month'])) {
        // Add one month to the current booking
        $extendBookingQuery = "UPDATE bookings 
                               SET booking_date = DATE_ADD(booking_date, INTERVAL 1 MONTH)
                               WHERE userN = :username";
        $extendBookingStmt = $pdo->prepare($extendBookingQuery);
        $extendBookingStmt->execute(['username' => $username]);

        // Redirect to the same page to avoid form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Fetch the currently booked room for the logged-in user
    $currentBookingQuery = "SELECT room.*, bookings.building, bookings.booking_date 
                            FROM room 
                            JOIN bookings ON room.room_number = bookings.room_number AND room.building = bookings.building
                            WHERE bookings.userN = :username";
    $currentBookingStmt = $pdo->prepare($currentBookingQuery);
    $currentBookingStmt->execute(['username' => $username]);
    $bookedRoom = $currentBookingStmt->fetch(PDO::FETCH_ASSOC);

    if ($bookedRoom) {
        echo '<div class="booked-room">';
        echo '<h1>Текуща стая - Бл.' . htmlspecialchars($bookedRoom['building']) . '</h1>';
        echo '<div class="room-detail">';
        //echo '<img src="img/' . htmlspecialchars($bookedRoom['image_url']) . '" alt="Room Image">';
        echo '<p>Номер на стая: ' . htmlspecialchars($bookedRoom['room_number']) . '</p>';
        echo '<p>Капацитет: ' . htmlspecialchars($bookedRoom['room_capacity']) . '</p>';
        echo '<p>Цена: ' . htmlspecialchars($bookedRoom['price']) . 'лв. на месец' .'</p>';

        // Fetch the updated rating
        $ratingQuery = "SELECT total_rating, number_of_reviews FROM room WHERE room_number = :room_number AND building = :building";
        $ratingStmt = $pdo->prepare($ratingQuery);
        $ratingStmt->execute(['room_number' => $bookedRoom['room_number'], 'building' => $bookedRoom['building']]);
        $ratingData = $ratingStmt->fetch(PDO::FETCH_ASSOC);

        $averageRating = $ratingData['number_of_reviews'] > 0 ? round($ratingData['total_rating'] / $ratingData['number_of_reviews'], 1) : 0;
        echo '<p>Рейтинг: ' . htmlspecialchars($averageRating) . '/5 (' . htmlspecialchars($ratingData['number_of_reviews']) . ' отзива)</p>';

        // Rating system
        if (isset($_POST['rate_room']) && isset($_POST['rating'])) {
            $rating = $_POST['rating'];
            $room_number = $bookedRoom['room_number'];
            $building = $bookedRoom['building'];

            // Update total_rating and number_of_reviews in room table
            $updateRoomQuery = "UPDATE room
                                SET total_rating = total_rating + :rating, 
                                    number_of_reviews = number_of_reviews + 1
                                WHERE room_number = :room_number AND building = :building";
            $updateRoomStmt = $pdo->prepare($updateRoomQuery);
            $updateRoomStmt->execute(['rating' => $rating, 'room_number' => $room_number, 'building' => $building]);

            // Set session variable to indicate rating is done
            $_SESSION['has_rated'] = $room_number;
            $_SESSION['user_rating'] = $rating;

            // Redirect to the same page to avoid form resubmission
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        // Show star rating form if the user has not rated the room
        if (!isset($_SESSION['has_rated']) || $_SESSION['has_rated'] != $bookedRoom['room_number']) {
            echo '<form method="post" action="">';
            echo '<p for="rating">Оцени стаята:</p>';
            echo '<div class="rating">';
            for ($i = 5; $i >= 1; $i--) {
                echo '<input type="radio" id="star' . $i . '" name="rating" value="' . $i . '">';
                echo '<label for="star' . $i . '">☆</label>';
            }
            
            echo '</div>';
            if(isset($_POST['rate_room']) && !isset($_POST['rating'])) {
                echo '<p style="color: red;">' . 'Please select a rating' . '</p>';
            }
            echo '<button type="submit" name="rate_room" class="button">Изпратете оценка</button><br><br>';
            echo '</form>';
        } else {
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

        echo '<div class="button-container">';
        echo '<form method="post" action="" style="flex: 1; margin-right: 10px;">';
        echo '<button type="submit" name="pay_next_month" class="button">Плати за следващия месец</button>';
        echo '</form>';
        
        echo '<form method="post" action="" style="flex: 1;">';
        echo '<button type="submit" name="cancel_booking" class="button button-red">Прекрати престой</button>';
        echo '</form>';
        echo '</div>';

        echo '</div>';
        echo '</div>';
    }
}
?>