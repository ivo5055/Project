<?php
include 'dbh.inc.php'; // Include your database connection file

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Automatically cancel expired bookings 1 Week in advance
    $currentDateTime = new DateTime();
    $expirationDateTime = (clone $currentDateTime)->sub(new DateInterval('P1W'));

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
    
    // Display booking date at the top right
    echo '<div class="expiration-date">Срокът изтича на: ' . htmlspecialchars($bookedRoom['booking_date']) . '</div>';
    
    echo '<p>Номер на стая: ' . htmlspecialchars($bookedRoom['room_number']) . '</p>';
    echo '<p>Капацитет: ' . htmlspecialchars($bookedRoom['room_capacity']) . '</p>';
    echo '<p>Цена: ' . htmlspecialchars($bookedRoom['price']) . 'лв. на месец' . '</p>';

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
        echo '<div class="rating">';
        for ($i = 5; $i >= 1; $i--) {
            echo '<input type="radio" id="star' . $i . '" name="rating" value="' . $i . '">';
            echo '<label for="star' . $i . '">☆</label>';
        }
        echo '</div>';
        echo '<button type="submit" name="rate_room" class="button">Изпрати Рейтинг</button> ';
        if (isset($_POST['rate_room']) && !isset($_POST['rating'])) {
            echo '<p style="color: red;">' . 'Please select a rating' . '</p>';
        }
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

    echo '<form method="post" action="" style="flex: 1;">';
    echo '<button type="submit" name="cancel_booking" class="button button-red">Прекрати Престой</button> <br><br>';
    
    echo '<div class="button-container">';
    echo '<div id="paypal-button-container" style="flex: 1; margin-right: 10px;"></div>';
    echo '</div>';

    echo '</form>';
    echo '</div>';
    echo '</div>';
}}
?>

<script src="https://www.paypal.com/sdk/js?client-id=AWMHbixiflAFoFCPWHvEpXz8hcqJvwMxwEnObijMCUif6Q3csc-LSJ7am9BhxoGGYkBrlaOstBTuhlqd"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Render the PayPal button into #paypal-button-container
    paypal.Buttons({
        createOrder: function(data, actions) {
            // Create a PayPal order
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?= htmlspecialchars($bookedRoom['price']) ?>', // The amount to be paid
                        currency_code: 'USD'
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            // Capture the payment
            return actions.order.capture().then(function(details) {
                // Send the transaction details to your server
                fetch('includes/pay_next_month.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        orderID: data.orderID
                    })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        // Reload or redirect after successful payment
                        window.location.reload();
                    } else {
                        alert('Payment failed: ' + result.error);
                    }
                });
            });
        },
        onError: function(err) {
            console.error('PayPal error:', err);
        }
    }).render('#paypal-button-container');
});
</script>
